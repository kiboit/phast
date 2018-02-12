<?php


namespace Kibo\Phast\Parsing\HTML\HTMLStreamElements;


abstract class Tag implements Element {

    /**
     * @var int
     */
    protected $startStreamOffset;

    /**
     * @var int
     */
    protected $endStreamOffset;

    /**
     * @var string
     */
    protected $tagName;

    public function __construct($startStreamOffset, $endStreamOffset, $tagName) {
        $this->startStreamOffset = $startStreamOffset;
        $this->endStreamOffset = $endStreamOffset;
        $this->tagName = $tagName;
    }

    /**
     * @return int
     */
    public function getStartStreamOffset() {
        return $this->startStreamOffset;
    }

    /**
     * @return int
     */
    public function getEndStreamOffset() {
        return $this->endStreamOffset;
    }

    /**
     * @return string
     */
    public function getTagName() {
        return $this->tagName;
    }

    public function output() {
        // TODO: Implement output() method.
    }
}
