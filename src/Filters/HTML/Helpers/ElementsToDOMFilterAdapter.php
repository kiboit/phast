<?php


namespace Kibo\Phast\Filters\HTML\Helpers;

use Kibo\Phast\Common\DOMDocument;
use Kibo\Phast\Filters\HTML\HTMLPageContext;
use Kibo\Phast\Parsing\HTML\HTMLStream;

trait ElementsToDOMFilterAdapter {

    public function transformElements(HTMLPageContext $context) {
        return $context->getElements();
    }

    public function transformHTMLDOM(DOMDocument $document) {
        $context = new HTMLPageContext($document->getBaseURL(), $document->getStream()->getElements());
        $stream = new HTMLStream();
        $document->setStream($stream);
        foreach ($this->transformElements($context) as $element) {
            $stream->addElement($element);
        }
        foreach ($context->getPhastJavaScripts() as $script) {
            $document->addPhastJavaScript($script);
        }

    }



}
