<?php
namespace Kibo\Phast\Build;

use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassLike;
use PhpParser\Node\Stmt\Interface_;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\Node\Stmt\Use_;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\ParserFactory;

class Compiler {
    private $include = [];

    private $exclude = [];

    private $resources = [];

    public static function getPhastCompiler(): self {
        $root = __DIR__ . '/../..';

        return (new self())
            ->include($root . '/src')
            ->exclude($root . '/src/Build')
            ->addResource('cacert.pem', __DIR__ . '/../HTTP/cacert.pem');
    }

    public static function getDirectoryByClass($class): string {
        $reflect = new \ReflectionClass($class);
        return dirname($reflect->getFilename());
    }

    public function include(string $dir): self {
        $this->include[] = $dir;
        return $this;
    }

    public function exclude(string $dir): self {
        $this->exclude[] = $dir;
        return $this;
    }

    public function addResource(string $name, string $filename): self {
        $contents = file_get_contents($filename);
        if ($contents === false) {
            throw new \RuntimeException("Could not read file: $filename");
        }
        $this->resources[$name] = $contents;
        return $this;
    }

    public function getResult(): string {
        $combinedTree = [];

        /** @var \SplFileInfo $fileinfo */
        foreach ($this->getSourceFiles() as $fileinfo) {
            $tree = $this->parseFile($fileinfo);
            $namespace = $this->getSingleNamespace($tree, $fileinfo);
            $namespace->file = $fileinfo;
            $nameResolver = new NameResolver();
            $nodeTraverser = new NodeTraverser();
            $nodeTraverser->addVisitor($nameResolver);
            $nodeTraverser->traverse([$namespace]);
            $nodeTraverser = new NodeTraverser();
            $nodeTraverser->addVisitor(new ScriptInliner());
            $nodeTraverser->traverse([$namespace]);
            $namespace->stmts = [$this->getClassLike($namespace)];
            $combinedTree[] = $namespace;
        }

        $combinedTree = iterator_to_array($this->reorderDefinitions($combinedTree), false);

        return (new ASCIIPrettyPrinter())->prettyPrintFile($combinedTree);
    }

    public function compile(string $outputDirectory): void {
        @mkdir($outputDirectory);
        $this->writeFile($outputDirectory . '/phast.php', $this->getResult());
        foreach ($this->resources as $filename => $contents) {
            $this->writeFile($outputDirectory . '/' . $filename, $contents);
        }
    }

    public function writeFile(string $filename, string $contents): void {
        $temp = $filename . '~' . getmypid();
        try {
            if (file_put_contents($temp, $contents) !== strlen($contents)) {
                throw new \RuntimeException("Could not write output file: $temp");
            }
            if (!rename($temp, $filename)) {
                throw new \RuntimeException("Could not rename output file: $temp");
            }
        } finally {
            @unlink($temp);
        }
    }

    private function getSourceFiles(): array {
        $files = iterator_to_array($this->getUnsortedSourceFiles(), false);
        usort($files, function (\SplFileInfo $a, \SplFileInfo $b): int {
            return $a->getPathname() <=> $b->getPathname();
        });
        return $files;
    }

    private function getUnsortedSourceFiles() {
        foreach ($this->include as $dir) {
            /** @var SplFileInfo $fileinfo */
            foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir))
                     as $fileinfo
            ) {
                foreach ($this->exclude as $exclude) {
                    if (strpos($fileinfo->getPathname(), $exclude . '/') === 0) {
                        continue 2;
                    }
                }
                if (preg_match('/^[A-Z].*\.php$/', $fileinfo->getFilename())) {
                    yield $fileinfo;
                }
            }
        }
    }

    private function parseFile(\SplFileInfo $fileinfo) {
        $php = file_get_contents($fileinfo->getPathname());
        if ($php === false) {
            throw new \RuntimeException(sprintf('%s: Could not read source file', $fileinfo->getPathname()));
        }
        $parser = (new ParserFactory())->create(ParserFactory::ONLY_PHP7);
        try {
            $tree = $parser->parse($php);
        } catch (\Exception $e) {
            throw new \RuntimeException(sprintf(
                '%s: Caught %s: %s',
                $fileinfo->getPathname(),
                get_class($e),
                $e->getMessage()
            ), 0, $e);
        }
        return $tree;
    }

    /** @return Namespace_ */
    private function getSingleNamespace(array $tree, \SplFileInfo $fileinfo) {
        if (sizeof($tree) !== 1
            || !($tree[0] instanceof Namespace_)
        ) {
            throw new \RuntimeException(sprintf(
                'Expected a single namespace {}, instead got %s',
                $tree ? implode(', ', array_map('get_class', $tree)) : 'none'
            ));
        }
        return $tree[0];
    }

    /** @return ClassLike */
    private function getClassLike(Namespace_ $namespace) {
        $classes = [];
        foreach ($namespace->stmts as $node) {
            if ($node instanceof Use_); elseif ($node instanceof ClassLike) {
                $classes[] = $node;
            } else {
                throw new \RuntimeException("Unexpected top-level node: {$node}");
            }
        }
        if (sizeof($classes) !== 1) {
            $names = array_map(function (ClassLike $class) {
                return $class->name;
            }, $classes);
            throw new \RuntimeException(sprintf(
                'Expected a single declaration, instead got %s',
                $classes ? implode(', ', $names) : 'none'
            ));
        }
        return $classes[0];
    }

    /** @param Namespace_[] $namespaces */
    private function reorderDefinitions(array $namespaces) {
        $allNames = [];
        foreach ($namespaces as $namespace) {
            $allNames[$this->getClassLike($namespace)->namespacedName->toString()] = true;
        }
        foreach ($namespaces as $namespace) {
            $class = $this->getClassLike($namespace);
            foreach ($this->getFullyQualifiedNames($this->getDependencies($class)) as $dependency) {
                if (strpos($dependency, '\\') !== false && !isset($allNames[$dependency])) {
                    throw new \RuntimeException("{$class->namespacedName} depends on undeclared {$dependency}");
                }
            }
        }
        return $this->doReorderDefinitions($namespaces);
    }

    private function doReorderDefinitions(array $namespaces, array $declared = []) {
        $undeclared = [];
        foreach ($namespaces as $namespace) {
            $class = $this->getClassLike($namespace);
            $dependencies = $this->getFullyQualifiedNames($this->getDependencies($class));
            $undeclaredDependencies = iterator_to_array($this->getUndeclaredDependencies($dependencies, $declared), false);
            if (!$undeclaredDependencies) {
                $declared[] = $class->namespacedName->toString();
                yield $namespace;
            } else {
                $undeclared[] = $namespace;
            }
        }
        if ($undeclared) {
            yield from $this->doReorderDefinitions($undeclared, $declared);
        }
    }

    /**
     * @param \Traversable|Name[] $names
     * @return \Generator|string[]
     */
    private function getFullyQualifiedNames($names) {
        foreach ($names as $name) {
            if (!$name->isFullyQualified()) {
                throw new \RuntimeException("Got unresolved name {$name}");
            }
            yield $name->toString();
        }
    }

    private function getDependencies(ClassLike $class) {
        if ($class instanceof Interface_) {
            yield from $class->extends;
        }
        if ($class instanceof Class_) {
            if ($class->extends) {
                yield $class->extends;
            }
            yield from $class->implements;
        }
    }

    private function getUndeclaredDependencies($names, $declared) {
        foreach ($names as $name) {
            if (strpos($name, '\\') !== false && !in_array($name, $declared)) {
                yield $name;
            }
        }
    }
}
