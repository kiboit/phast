<?php
namespace Kibo\Phast\Build;

use Kibo\Phast\Common\JSMinifier;
use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\ClassLike;
use PhpParser\NodeVisitorAbstract;

class ScriptInliner extends NodeVisitorAbstract {

    /** @var ClassLike|null */
    private $class;

    public function enterNode(Node $node) {
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
        if (!$this->class) {
            throw new \RuntimeException(
                "Expected to be inside class when seeing call with filename");
        }
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
        $proc = proc_open(__DIR__ . '/../../node_modules/.bin/uglifyjs --rename',
                          [['pipe', 'r'], ['pipe', 'w'], STDERR], $pipes);

        if (!$proc) {
            throw new \RuntimeException("Failed to start uglifyjs");
        }

        fwrite($pipes[0], $source);
        fclose($pipes[0]);

        $output = stream_get_contents($pipes[1]);
        fclose($pipes[1]);

        $status = proc_close($proc);
        if ($status) {
            throw new RuntimeException("uglifyjs exited with status {$status}");
        }

        return $output;
    }

    public function leaveNode(Node $node) {
        if ($node instanceof ClassLike) {
            $this->class = null;
        }
    }

    private function getFilenameFromValue($value) {
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
        $nsParts = explode('\\', $this->class->namespacedName);
        $nsParts = array_slice($nsParts, 2);
        array_pop($nsParts);
        return implode('/', array_merge(['src'], $nsParts, [substr($value->right->value, 1)]));
    }

}
