<?php


namespace Kibo\Phast\Filters\HTML\PhastScriptsCompiler;


use Kibo\Phast\Common\PhastJavaScriptCompiler;
use Kibo\Phast\Filters\HTML\HTMLPageContext;
use Kibo\Phast\Filters\HTML\HTMLStreamFilter;
use Kibo\Phast\Parsing\HTML\HTMLStreamElements\ClosingTag;
use Kibo\Phast\Parsing\HTML\HTMLStreamElements\Element;
use Kibo\Phast\Parsing\HTML\HTMLStreamElements\Tag;

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
            if ($this->isClosingBodyTag($element) || $buffering) {
                $buffered[] = $element;
                $buffering = true;
            } else {
                yield $element;
            }
        }

        $scripts = $context->getPhastJavaScripts();
        if (!empty ($scripts)) {
            $compiled = $this->compiler->compileScriptsWithConfig($scripts);
            $script = new Tag('script');
            $script->setTextContent($compiled);
            yield $script;
        }

        foreach ($buffered as $element) {
            yield $element;
        }
    }

    private function isClosingBodyTag(Element $element) {
        return $element instanceof ClosingTag && $element->getTagName() == 'body';
    }
}
