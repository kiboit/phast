<?php


namespace Kibo\Phast\Parsing\HTML;

use Kibo\Phast\Parsing\HTML\HTMLStreamElements\Element;

class HTMLStream {

    /**
     * @var Element[]
     */
    private $elements = [];

    /**
     * @param Element $element
     */
    public function addElement(Element $element) {
        $this->elements[] = $element;
        $element->setStream($this);
    }

    public function insertBeforeElement(Element $reference, Element $toInsert) {
        $index = array_search($reference, $this->elements, true);
        array_splice($this->elements, $index, 0, [$toInsert]);
    }


    /**
     * @return Element[]
     */
    public function getElements() {
        return $this->elements;
    }
}
