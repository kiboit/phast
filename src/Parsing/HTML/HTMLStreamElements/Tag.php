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

    public function __construct($tagName) {
        $this->tagName = $tagName;
    }


    public function setStreamOffsets($startOffset, $endOffset) {
        $this->startStreamOffset = $startOffset;
        $this->endStreamOffset = $endOffset;
    }

    /**
     * @return string
     */
    public function getTagName() {
        return $this->tagName;
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

    public function output() {
        // TODO: Implement output() method.
    }
}
