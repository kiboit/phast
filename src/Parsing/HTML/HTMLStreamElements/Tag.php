<?php


namespace Kibo\Phast\Parsing\HTML\HTMLStreamElements;


abstract class Tag extends Element {

    /**
     * @var string
     */
    private $tagName;

    public function __construct($tagName) {
        $this->tagName = $tagName;
    }

    /**
     * @return string
     */
    public function getTagName() {
        return $this->tagName;
    }
}
