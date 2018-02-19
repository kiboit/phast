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
            $element->setPrevious($this->tail);
            $this->tail->setNext($element);
        }
        $this->tail = $element;
    }

    public function remove(Element $element) {
        $prev = $element->getPrevious();
        $next = $element->getNext();
        if ($next) {
            $next->setPrevious($prev);
        }
        if ($prev) {
            $prev->setNext($next);
        }
    }

    public function insertBefore(Element $reference, Element $toInsert) {
        $prev = $reference->getPrevious();
        if ($prev) {
            $toInsert->setPrevious($prev);
            $prev->setNext($toInsert);
        }
        $reference->setPrevious($toInsert);
        $toInsert->setNext($reference);
    }

    public function getIterator() {
        $next = $this->head;
        while ($next) {
            yield $next;
            $next = $next->getNext();
        }
    }

    public function getReverseIterator() {
        $prev = $this->tail;
        while ($prev) {
            yield $prev;
            $prev = $prev->getPrevious();
        }
    }

}
