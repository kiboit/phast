<?php

namespace Kibo\Phast\Filters\HTML;

abstract class RearrangementHTMLFilter implements HTMLFilter {

    abstract protected function getElementsToRearrange(\DOMDocument $document);

    public function transformHTMLDOM(\DOMDocument $document) {
        $body = $this->getBodyElement($document);
        $elements = iterator_to_array($this->getElementsToRearrange($document));
        foreach ($elements as $element) {
            $body->appendChild($element);
        }
    }

    /**
     * @param \DOMDocument $document
     * @return \DOMElement
     * @throws \Exception
     */
    private function getBodyElement(\DOMDocument $document) {
        $bodies = iterator_to_array($document->getElementsByTagName('body'));
        if (count($bodies) == 0) {
            throw new \Exception('No body tag found in document');
        }
        return $bodies[0];
    }

}
