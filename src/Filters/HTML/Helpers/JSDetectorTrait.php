<?php

namespace Kibo\Phast\Filters\HTML\Helpers;

use Kibo\Phast\Parsing\HTML\HTMLStreamElements\Tag;

trait JSDetectorTrait {


    /**
     * @param Tag $element
     * @return bool|false|int
     */
    private function isJSElement(Tag $element) {
        if (!$element->hasAttribute('type')) {
            return true;
        }
        return preg_match('~^(text|application)/javascript(;|$)~i', $element->getAttribute('type'));
    }

}
