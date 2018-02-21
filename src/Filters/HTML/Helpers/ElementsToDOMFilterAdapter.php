<?php


namespace Kibo\Phast\Filters\HTML\Helpers;

use Kibo\Phast\Common\DOMDocument;
use Kibo\Phast\Filters\HTML\HTMLPageContext;
use Kibo\Phast\Parsing\HTML\HTMLStream;

trait ElementsToDOMFilterAdapter {

    public function transformElements(HTMLPageContext $context, \Traversable $elements) {
        return $elements;
    }

    public function transformHTMLDOM(DOMDocument $document) {
        $elements = $document->getStream()->getElements();
        $context = new HTMLPageContext($document->getBaseURL());
        $stream = new HTMLStream();
        $document->setStream($stream);
        foreach ($this->transformElements($context, $elements) as $element) {
            $stream->addElement($element);
        }
        foreach ($context->getPhastJavaScripts() as $script) {
            $document->addPhastJavaScript($script);
        }

    }



}
