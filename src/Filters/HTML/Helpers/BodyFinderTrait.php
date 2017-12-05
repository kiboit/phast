<?php

namespace Kibo\Phast\Filters\HTML\Helpers;

trait BodyFinderTrait {

    /**
     * @param \Kibo\Phast\Common\DOMDocument $document
     * @return \DOMElement
     * @throws \Exception
     */
    private function getBodyElement(\Kibo\Phast\Common\DOMDocument $document) {
        $bodies = iterator_to_array($document->query('//body'));
        if (count($bodies) == 0) {
            throw new \Exception('No body tag found in document');
        }
        return $bodies[0];
    }

}
