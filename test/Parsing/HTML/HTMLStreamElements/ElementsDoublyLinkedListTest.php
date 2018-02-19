<?php

namespace Kibo\Phast\Parsing\HTML\HTMLStreamElements;


class ElementsDoublyLinkedListTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var ElementsDoublyLinkedList
     */
    private $list;

    public function setUp() {
        parent::setUp();
        $this->list = new ElementsDoublyLinkedList();
    }

    public function testAdding() {
        list ($e1, $e2, $e3) =  $this->makeAndAdd(3);

        $this->assertSame($e1->next, $e2);
        $this->assertSame($e2->next, $e3);
        $this->assertSame($e3->previous, $e2);
        $this->assertSame($e2->previous, $e1);
        $this->assertNull($e1->previous);
        $this->assertNull($e3->next);
    }

    public function testRemovingMiddle() {
        list ($e1, $e2, $e3) = $this->makeAndAdd(3);

        $this->list->remove($e2);
        $this->assertSame($e1->next, $e3);
        $this->assertSame($e3->previous, $e1);
    }

    public function testRemovingTail() {
        list ($e1, $e2) = $this->makeAndAdd(2);
        $this->list->remove($e2);
        $this->assertNull($e1->next);
    }

    public function testRemovingHead() {
        list ($e1, $e2) = $this->makeAndAdd(2);
        $this->list->remove($e1);
        $this->assertNull($e2->previous);
    }

    public function testInsertInMiddle() {
        list ($e1, $e3) = $this->makeAndAdd(2);
        $e2 = new Element();
        $this->list->insertBefore($e3, $e2);
        $this->assertSame($e1->next, $e2);
        $this->assertSame($e2->next, $e3);
        $this->assertSame($e3->previous, $e2);
        $this->assertSame($e2->previous, $e1);
    }

    public function testInsertInsertBeforeHead() {
        $e2 = new Element();
        $this->list->add($e2);
        $e1 = new Element();
        $this->list->insertBefore($e2, $e1);
        $this->assertNull($e1->previous);
        $this->assertSame($e1->next, $e2);
        $this->assertSame($e2->previous, $e1);
    }

    public function testIterator() {
        $count = 10;
        $elements = $this->makeAndAdd($count);
        $i = 0;
        foreach ($this->list as $element) {
            $this->assertSame($elements[$i++], $element);
        }
        $this->assertEquals($count, $i);
    }

    public function testReverseIterator() {
        $count = 10;
        $elements = $this->makeAndAdd($count);
        $i = 9;
        foreach ($this->list->getReverseIterator() as $element) {
            $this->assertSame($elements[$i--], $element);
        }
        $this->assertEquals(-1, $i);
    }



    private function makeElements($count) {
        $elements = [];
        for ($i = 0; $i < $count; $i++) {
            $elements[] = new Element();
        }
        return $elements;
    }

    /**
     * @param Element[] $elements
     */
    private function addElements(array $elements) {
        foreach ($elements as $element) {
            $this->list->add($element);
        }
    }

    /**
     * @param $count
     * @return Element[]
     */
    private function makeAndAdd($count) {
        $elements = $this->makeElements($count);
        $this->addElements($elements);
        return $elements;
    }


}
