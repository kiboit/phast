<?php


namespace Kibo\Phast\Filters\HTML\PhastScriptsCompiler;

use Kibo\Phast\Filters\HTML\HTMLPageContext;
use Kibo\Phast\Filters\HTML\HTMLStreamFilter;
use Kibo\Phast\Parsing\HTML\HTMLStreamElements\ClosingTag;
use Kibo\Phast\Parsing\HTML\HTMLStreamElements\Element;
use Kibo\Phast\Parsing\HTML\HTMLStreamElements\Tag;
use Kibo\Phast\ValueObjects\PhastJavaScript;

class Filter implements HTMLStreamFilter {
    /**
     * @var PhastJavaScriptCompiler
     */
    private $compiler;

    /**
     * @var ?string
     */
    private $cspNonce;

    /**
     * Filter constructor.
     * @param PhastJavaScriptCompiler $compiler
     * @param ?string $cspNonce
     */
    public function __construct(PhastJavaScriptCompiler $compiler, $cspNonce) {
        $this->compiler = $compiler;
        $this->cspNonce = $cspNonce;
    }

    public function transformElements(\Traversable $elements, HTMLPageContext $context) {
        $buffer = [];
        $buffering = false;
        foreach ($elements as $element) {
            if ($this->isClosingBodyTag($element)) {
                if ($buffering) {
                    foreach ($buffer as $bufElement) {
                        yield $bufElement;
                    }
                    $buffer = [];
                }
                $buffering = true;
            }
            if ($buffering) {
                $buffer[] = $element;
            } else {
                yield $element;
            }
        }

        $scripts = $context->getPhastJavaScripts();
        if (!empty($scripts)) {
            yield $this->compileScript($scripts);
        }

        foreach ($buffer as $element) {
            yield $element;
        }
    }

    /**
     * @param PhastJavaScript[] $scripts
     * @return Tag
     */
    private function compileScript(array $scripts) {
        $names = array_map(function (PhastJavaScript $script) {
            $matches = [];
            preg_match('~[^/]*?\/?[^/]+$~', $script->getFilename(), $matches);
            return $matches[0];
        }, $scripts);
        $script = new Tag('script');
        $script->setAttribute('data-phast-compiled-js-names', join(',', $names));
        if ($this->cspNonce !== null) {
            $script->setAttribute('nonce', $this->cspNonce);
        }
        $compiled = $this->compiler->compileScriptsWithConfig($scripts);
        $script->setTextContent($compiled);
        return $script;
    }

    private function isClosingBodyTag(Element $element) {
        return $element instanceof ClosingTag && $element->getTagName() == 'body';
    }
}
