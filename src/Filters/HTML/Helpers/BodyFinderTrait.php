<?php

namespace Kibo\Phast\Filters\HTML\Helpers;

use Kibo\Phast\Common\DOMDocument;

trait BodyFinderTrait {

    /**
     * @param DOMDocument $document
     * @return \DOMElement
     * @throws \Exception
     */
    private function getBodyElement(DOMDocument $document) {
        $bodies = iterator_to_array($document->query('//body'));
        if (count($bodies) == 0) {
            throw new \Exception('No body tag found in document');
        }
        return $bodies[0];
    }

}
