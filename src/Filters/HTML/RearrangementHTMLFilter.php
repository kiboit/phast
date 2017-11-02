<?php

namespace Kibo\Phast\Filters\HTML;

use Kibo\Phast\Filters\HTML\Helpers\BodyFinderTrait;

abstract class RearrangementHTMLFilter implements HTMLFilter {
    use BodyFinderTrait;

    abstract protected function getElementsToRearrange(\DOMDocument $document);

    public function transformHTMLDOM(\DOMDocument $document) {
        $body = $this->getBodyElement($document);
        $elements = iterator_to_array($this->getElementsToRearrange($document));
        foreach ($elements as $element) {
            $body->appendChild($element);
        }
    }
}
