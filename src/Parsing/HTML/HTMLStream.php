<?php


namespace Kibo\Phast\Parsing\HTML;

use Kibo\Phast\Parsing\HTML\HTMLStreamElements\ClosingTag;
use Kibo\Phast\Parsing\HTML\HTMLStreamElements\Element;
use Kibo\Phast\Parsing\HTML\HTMLStreamElements\OpeningTag;
use Kibo\Phast\Parsing\HTML\HTMLStreamElements\TagCollection;
use Kibo\Phast\Parsing\HTML\HTMLStreamElements\TextContainingTag;

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
        $index = $this->getElementIndex($reference);
        array_splice($this->elements, $index, 0, [$toInsert]);
    }

    /**
     * @param OpeningTag $tag
     * @return ClosingTag | null
     */
    public function getClosingTag(OpeningTag $tag) {
        $startIndex = $this->getElementIndex($tag);
        for ($i = $startIndex; $i < count($this->elements); $i++) {
            /** @var ClosingTag $element */
            $element = $this->elements[$i];
            if (($element instanceof ClosingTag) && $element->getTagName()) {
                return $element;
            }
        }
    }

    /**
     * @param $name
     * @return TagCollection
     */
    public function getElementsByTagName($name) {
        $tags = array_filter($this->elements, function (Element $element) use ($name) {
            return (($element instanceof OpeningTag) || ($element instanceof TextContainingTag))
                   && $element->getTagName() == $name;

        });
        return new TagCollection(array_values($tags));
    }


    /**
     * @return Element[]
     */
    public function getElements() {
        return $this->elements;
    }

    public function getElementIndex(Element $element) {
        return array_search($element, $this->elements, true);
    }
}
