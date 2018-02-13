<?php

namespace Kibo\Phast\Filters\HTML\Helpers;

use Kibo\Phast\Common\DOMDocument;

trait BodyFinderTrait {

    /**
     * @param DOMDocument $document
     * @return \Kibo\Phast\Parsing\HTML\HTMLStreamElements\ClosingTag|null
     * @throws \Exception
     */
    private function getBodyElement(DOMDocument $document) {
        $body = $document->getElementsByTagName('body')->item(0);
        if (is_null($body)) {
            throw new \Exception('No body tag found in document');
        }
        $bodyClosing = $document->getStream()->getClosingTag($body);
        if (is_null($bodyClosing)) {
            throw new \Exception('No body tag found in document');
        }
        return $bodyClosing;
    }

}
