<?php


namespace Kibo\Phast\Parsing\HTML\HTMLStreamElements;


class TagCollection implements \ArrayAccess, \Iterator, \Countable {

    /**
     * @var Tag[]
     */
    private $tags;

    private $currentIndex = 0;

    /**
     * TagCollection constructor.
     * @param Tag[] $tags
     */
    public function __construct(array $tags) {
        $this->tags = $tags;
    }

    public function item($index) {
        return isset ($this->tags[$index]) ? $this->tags[$index] : null;
    }

    public function __get($name) {
        if ($name == 'length') {
            return $this->count();
        }
    }

    public function current() {
        return $this->tags[$this->currentIndex];
    }

    public function next() {
        $this->currentIndex++;
    }

    public function key() {
        return $this->currentIndex;
    }

    public function valid() {
        return isset ($this->tags[$this->currentIndex]);
    }

    public function rewind() {
        $this->currentIndex = 0;
    }

    public function offsetExists($offset) {
        return isset ($this->tags[$offset]);
    }

    public function offsetGet($offset) {
        return $this->item($offset);
    }

    public function offsetSet($offset, $value) {
        $this->tags[$offset] = $value;
    }

    public function offsetUnset($offset) {
        unset ($this->tags[$offset]);
    }

    public function count() {
        return count($this->tags);
    }


}
