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
     * Filter constructor.
     * @param PhastJavaScriptCompiler $compiler
     */
    public function __construct(PhastJavaScriptCompiler $compiler) {
        $this->compiler = $compiler;
    }

    public function transformElements(\Traversable $elements, HTMLPageContext $context) {
        $buffered = [];
        $buffering = false;
        foreach ($elements as $element) {
            if ($this->isClosingBodyTag($element)) {
                if ($buffering) {
                    foreach ($buffered as $bufElement) {
                        yield $bufElement;
                    }
                    $buffered =  [];
                }
                $buffering = true;
            }
            if ($buffering) {
                $buffered[] = $element;
            } else {
                yield $element;
            }
        }

        $scripts = $context->getPhastJavaScripts();
        if (!empty($scripts)) {
            yield $this->compileScript($scripts);
        }

        foreach ($buffered as $element) {
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
        $compiled = $this->compiler->compileScriptsWithConfig($scripts);
        $script->setTextContent($compiled);
        return $script;
    }

    private function isClosingBodyTag(Element $element) {
        return $element instanceof ClosingTag && $element->getTagName() == 'body';
    }
}
