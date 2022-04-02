<?php
namespace Kibo\Phast\Build;

use Kibo\Phast\Cache\Sqlite\Cache;
use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\ClassLike;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\NodeVisitorAbstract;

class ScriptInliner extends NodeVisitorAbstract {
    /** @var Namespace_|null */
    private $namespace;

    /** @var ClassLike|null */
    private $class;

    /** @var Cache */
    private $cache;

    public function __construct() {
        $this->cache = new Cache([
            'cacheRoot' => sys_get_temp_dir() . '/Phast.ScriptInliner.' . posix_geteuid(),
            'maxSize' => 512 * 1024 * 1024,
        ], 'ScriptInliner');
    }

    public function enterNode(Node $node) {
        if ($node instanceof Namespace_) {
            $this->namespace = $node;
            return;
        }
        if ($node instanceof ClassLike) {
            $this->class = $node;
            return;
        }
        if ($node instanceof Node\Expr\StaticCall
            && !strcasecmp($node->class->toString(), 'Kibo\\Phast\\ValueObjects\\PhastJavaScript')
            && !strcasecmp($node->name, 'fromFile')
            && $node->args
            && ($value = $this->inlineValue($node->args[0]->value, $filename))
        ) {
            $node->args[0]->value = $value;
            array_unshift($node->args, new Node\Arg(new Node\Scalar\String_($filename)));
            $node->name = 'fromString';
        }
        if ($node instanceof Node\Expr\FuncCall
            && $node->name instanceof Name
            && !strcasecmp($node->name->toString(), 'file_get_contents')
            && ($value = $this->inlineValue($node->args[0]->value))
        ) {
            return $value;
        }
    }

    private function inlineValue($value, &$filename = null) {
        $file = $this->getFilenameFromValue($value);
        if ($file === null) {
            return;
        }
        $source = file_get_contents($file);
        if ($source === false) {
            throw new \RuntimeException("Failed to read file: $file");
        }
        if (preg_match('/\.js$/', $file)) {
            $source = $this->minifyScript($source);
        }
        $filename = $file;
        return new Node\Scalar\String_($source, ['kind' => Node\Scalar\String_::KIND_DOUBLE_QUOTED]);
    }

    private function minifyScript(string $source): string {
        return $this->cache->get('uglify:' . sha1($source), function () use ($source) {
            $proc = proc_open(
                __DIR__ . '/../../node_modules/.bin/uglifyjs --rename',
                [['pipe', 'r'], ['pipe', 'w'], STDERR],
                $pipes
            );

            if (!$proc) {
                throw new \RuntimeException('Failed to start uglifyjs');
            }

            fwrite($pipes[0], $source);
            fclose($pipes[0]);

            $output = stream_get_contents($pipes[1]);
            fclose($pipes[1]);

            $status = proc_close($proc);
            if ($status) {
                throw new \RuntimeException("uglifyjs exited with status {$status}");
            }

            return $output;
        });
    }

    public function leaveNode(Node $node) {
        if ($node instanceof ClassLike) {
            $this->class = null;
        }
        if ($node instanceof Namespace_) {
            $this->namespace = null;
        }
    }

    private function getFilenameFromValue($value) {
        if (!isset($this->namespace->file)) {
            throw new \RuntimeException('Namespace is missing file property');
        }
        if (!$value instanceof Node\Expr\BinaryOp\Concat) {
            return;
        }
        if (!$value->left instanceof Node\Scalar\MagicConst\Dir) {
            return;
        }
        if (!$value->right instanceof Node\Scalar\String_) {
            return;
        }
        if (strpos($value->right->value, '/') !== 0) {
            return;
        }
        return $this->namespace->file->getPath() . $value->right->value;
    }
}
