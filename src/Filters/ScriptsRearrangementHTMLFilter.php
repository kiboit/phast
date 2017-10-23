<?php

namespace Kibo\Phast\Filters;

class ScriptsRearrangementHTMLFilter implements HTMLFilter {

    public function transformHTMLDOM(\DOMDocument $document) {
        $body = $this->getBodyElement($document);
        $scripts = iterator_to_array($document->getElementsByTagName('script'));
        foreach ($scripts as $script) {
            if ($this->isJSElement($script)) {
                $body->appendChild($script);
            }
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
