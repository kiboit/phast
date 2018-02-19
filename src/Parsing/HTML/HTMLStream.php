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
     * @var Element[][]
     */
    private $elementsByTagName = [];

    /**
     * @var Element[]
     */
    private $elementsWithStyle = [];

    /**
     * @var Element[]
     */
    private $elementsWithClass = [];

    /**
     * @param Element $element
     */
    public function addElement(Element $element) {
        $this->elements[] = $element;
        $element->setStream($this);
        $this->addToIndexes($element);
    }

    public function insertBeforeElement(Element $reference, Element $toInsert) {
        $this->removeElement($toInsert);
        $index = $this->getElementIndex($reference);
        array_splice($this->elements, $index, 0, [$toInsert]);
        $this->addToIndexes($toInsert);
    }

    public function removeElement(Element $element) {
        $index = $this->getElementIndex($element);
        if ($index !== false) {
            array_splice($this->elements, $index, 1);
        }
        if (($element instanceof Tag) && isset ($this->elementsByTagName[$element->getTagName()])) {
            $indexIdx = array_search($element, $this->elementsByTagName[$element->getTagName()], true);
            if ($indexIdx !== false) {
                array_splice($this->elementsByTagName[$element->getTagName()], $indexIdx, 1);
            }
        }
    }

    /**
     * @param Tag $tag
     * @return ClosingTag | null
     */
    public function getClosingTag(Tag $tag) {
        $startIndex = $this->getElementIndex($tag);
        for ($i = count($this->elements) - 1; $i > $startIndex; $i--) {
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
        $tags = isset ($this->elementsByTagName[$name]) ? $this->elementsByTagName[$name] : [];
        return $this->makeTagCollection($tags);
    }

    /**
     * @param $attrName
     * @return TagCollection
     */
    public function getElementsWithAttr($attrName) {
        if ($attrName == 'style') {
            $tags = $this->elementsWithStyle;
        } else if ($attrName == 'class') {
            $tags = $this->elementsWithClass;
        } else {
            $tags = array_filter($this->elements, function (Element $element) use ($attrName) {
                return ($element instanceof Tag) && $element->hasAttribute($attrName);
            });
        }
        return $this->makeTagCollection($tags);
    }

    /**
     * @return Element[]
     */
    public function getElementsArray() {
        return $this->elements;
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

    private function addToIndexes(Element $tag) {
        if (!($tag instanceof Tag)) {
            return;
        }
        $this->elementsByTagName[$tag->getTagName()][] = $tag;
        if ($tag->hasAttribute('class')) {
            $this->elementsWithClass[] = $tag;
        }
        if ($tag->hasAttribute('style')) {
            $this->elementsWithStyle[] = $tag;
        }
    }
}
