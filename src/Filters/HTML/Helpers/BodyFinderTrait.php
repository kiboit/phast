<?php

namespace Kibo\Phast\Filters\HTML\Helpers;

use Kibo\Phast\Common\DOMDocument;
use Kibo\Phast\Parsing\HTML\HTMLStreamElements\ClosingTag;

trait BodyFinderTrait {

    /**
     * @param DOMDocument $document
     * @return \Kibo\Phast\Parsing\HTML\HTMLStreamElements\ClosingTag|null
     * @throws \Exception
     */
    private function getBodyElement(DOMDocument $document) {
        foreach ($document->getStream()->getElements()->getReverseIterator() as $tag) {
            if ($tag instanceof ClosingTag && $tag->getTagName() == 'body') {
                return $tag;
            }
        }
        throw new \Exception('No closing body tag found in document');
    }

}
