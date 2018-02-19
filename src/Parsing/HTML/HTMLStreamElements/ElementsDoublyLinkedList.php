<?php


namespace Kibo\Phast\Parsing\HTML\HTMLStreamElements;


class ElementsDoublyLinkedList implements \IteratorAggregate {

    /**
     * @var Element
     */
    private $head;

    /**
     * @var Element
     */
    private $tail;

    public function add(Element $element) {
        if (!isset ($this->head)) {
            $this->head = $element;
        }
        if (isset ($this->tail)) {
            $element->previous = $this->tail;
            $this->tail->next = $element;
        }
        $this->tail = $element;
    }

    public function remove(Element $element) {
        $prev = $element->previous;
        $next = $element->next;
        if ($next) {
            $next->previous = $prev;
        }
        if ($prev) {
            $prev->next = $next;
        }
    }

    public function insertBefore(Element $reference, Element $toInsert) {
        $prev = $reference->previous;
        if ($prev) {
            $toInsert->previous = $prev;
            $prev->next = $toInsert;
        }
        $reference->previous = $toInsert;
        $toInsert->next = $reference;
    }

    public function getIterator() {
        $next = $this->head;
        while ($next) {
            yield $next;
            $next = $next->next;
        }
    }

    public function getReverseIterator() {
        $prev = $this->tail;
        while ($prev) {
            yield $prev;
            $prev = $prev->previous;
        }
    }

}
