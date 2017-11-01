<?php

namespace Kibo\Phast\Filters\HTML\Helpers;

trait JSDetectorTrait {

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
