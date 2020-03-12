<?php

namespace Kibo\Phast\Filters\HTML\Helpers;

use Kibo\Phast\Parsing\HTML\HTMLStreamElements\Tag;

trait JSDetectorTrait {
    /**
     * @param Tag $element
     * @return bool
     */
    private function isJSElement(Tag $element) {
        if (!$element->hasAttribute('type')) {
            return true;
        }
        return (bool) preg_match('~^(text|application)/javascript(;|$)~i', $element->getAttribute('type'));
    }
}
