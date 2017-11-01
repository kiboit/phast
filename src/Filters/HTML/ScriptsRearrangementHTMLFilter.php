<?php

namespace Kibo\Phast\Filters\HTML;

class ScriptsRearrangementHTMLFilter extends RearrangementHTMLFilter {

    protected function getElementsToRearrange(\DOMDocument $document) {
        $scripts = $document->getElementsByTagName('script');
        foreach ($scripts as $script) {
            if ($this->isJSElement($script)) {
                yield $script;
            }
        }
    }

    /**
     * @param \DOMElement $element
     * @return bool|int
     */
    private function isJSElement(\DOMElement $element) {
        if (!$element->hasAttribute('type')) {
            return true;
        }
        return preg_match('~^(text|application)/javascript(;|$)~i', $element->getAttribute('type'));
    }

}
