<?php


namespace Kibo\Phast\Parsing\HTML;

use Kibo\Phast\Parsing\HTML\HTMLStreamElements\Element;
use Kibo\Phast\Parsing\HTML\HTMLStreamElements\ElementsDoublyLinkedList;
use Kibo\Phast\Parsing\HTML\HTMLStreamElements\Tag;
use Kibo\Phast\Parsing\HTML\HTMLStreamElements\TagCollection;

class HTMLStream {

    /**
     * @var ElementsDoublyLinkedList
     */
    private $elements;

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

    public function __construct() {
        $this->elements = new ElementsDoublyLinkedList();
    }

    /**
     * @param Element $element
     */
    public function addElement(Element $element) {
        $this->elements->add($element);
        $element->setStream($this);
        $this->addToIndexes($element);
    }

    /**
     * Inserts $toInsert before $reference
     *
     * @param Element $reference
     * @param Element $toInsert
     */
    public function insertBeforeElement(Element $reference, Element $toInsert) {
        $this->removeElement($toInsert);
        $this->elements->insertBefore($reference, $toInsert);
        $this->addToIndexes($toInsert);
    }

    public function removeElement(Element $element) {
        $this->elements->remove($element);
        if (
            ($element instanceof Tag)
            &&
            isset ($this->elementsByTagName[$element->getTagName()])
            && isset ($this->elementsByTagName[$element->getTagName()][spl_object_hash($element)])
        ) {
            unset ($this->elementsByTagName[$element->getTagName()][spl_object_hash($element)]);
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
            $tags = [];
            foreach ($this->elements as $element) {
                if ($element instanceof Tag && $element->hasAttribute($attrName)) {
                    $tags[] = $element;
                }
            }
        }
        return $this->makeTagCollection($tags);
    }

    /**
     * @return ElementsDoublyLinkedList
     */
    public function getElements() {
        return $this->elements;
    }

    /**
     * @return Element[]
     */
    public function getElementsArray() {
        return iterator_to_array($this->elements);
    }


    /**
     * @return TagCollection
     */
    public function getAllElementsTagCollection() {
        return $this->makeTagCollection($this->getElementsArray());
    }

    /**
     * @param Element $element1
     * @param Element $element2
     * @return TagCollection
     */
    public function getElementsBetween(Element $element1, Element $element2) {
        $index1 = $this->getElementIndex($element1);
        $index2 = $this->getElementIndex($element2);
        $tags = array_slice($this->getElementsArray(), $index1 + 1, $index2 - $index1 -1);
        return $this->makeTagCollection($tags);
    }

    public function getElementIndex(Element $element) {
        return array_search($element, $this->getElementsArray(), true);
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
        $this->elementsByTagName[$tag->getTagName()][spl_object_hash($tag)] = $tag;
        if ($tag->hasAttribute('class')) {
            $this->elementsWithClass[spl_object_hash($tag)] = $tag;
        }
        if ($tag->hasAttribute('style')) {
            $this->elementsWithStyle[spl_object_hash($tag)] = $tag;
        }
    }
}
