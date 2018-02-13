<?php


namespace Kibo\Phast\Parsing\HTML;

use Kibo\Phast\Parsing\HTML\HTMLStreamElements\Tag;
use Kibo\Phast\Parsing\HTML\HTMLStreamElements\ClosingTag;
use Kibo\Phast\Parsing\HTML\HTMLStreamElements\Element;
use Kibo\Phast\Parsing\HTML\HTMLStreamElements\TagCollection;

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
        $this->removeElement($toInsert);
        $index = $this->getElementIndex($reference);
        array_splice($this->elements, $index, 0, [$toInsert]);
    }

    public function removeElement(Element $element) {
        $index = $this->getElementIndex($element);
        if ($index !== false) {
            array_splice($this->elements, $index, 1);
        }
    }

    /**
     * @param Tag $tag
     * @return ClosingTag | null
     */
    public function getClosingTag(Tag $tag) {
        $startIndex = $this->getElementIndex($tag);
        for ($i = $startIndex; $i < count($this->elements); $i++) {
            /** @var ClosingTag $element */
            $element = $this->elements[$i];
            if (($element instanceof ClosingTag) && $element->getTagName() == $tag->getTagName()) {
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
            return ($element instanceof Tag) && $element->getTagName() == $name;

        });
        return $this->makeTagCollection($tags);
    }

    /**
     * @param $attrName
     * @return TagCollection
     */
    public function getElementsWithAttr($attrName) {
        $tags = array_filter($this->elements, function (Element $element) use ($attrName) {
            return ($element instanceof Tag) && $element->hasAttribute($attrName);
        });
        return $this->makeTagCollection($tags);
    }


    /**
     * @return TagCollection
     */
    public function getAllElements() {
        return $this->makeTagCollection($this->elements);
    }

    public function getElementsBetween(Element $element1, Element $element2) {
        $index1 = $this->getElementIndex($element1);
        $index2 = $this->getElementIndex($element2);
        $tags = array_slice($this->elements, $index1 + 1, $index2 - $index1 -1);
        return $this->makeTagCollection($tags);
    }

    public function getElement($index) {
        return $this->elements[$index];
    }

    public function getElementIndex(Element $element) {
        return array_search($element, $this->elements, true);
    }

    /**
     * @param Tag[] $tags
     * @return TagCollection
     */
    private function makeTagCollection(array $tags) {
        return new TagCollection(array_values($tags));
    }
}
