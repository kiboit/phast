<?php


namespace Kibo\Phast\Parsing\HTML\HTMLStreamElements;

class ClosingTag extends Element {
    /**
     * @var string
     */
    private $tagName;

    /**
     * ClosingTag constructor.
     * @param string $tagName
     */
    public function __construct($tagName) {
        $this->tagName = strtolower($tagName);
    }

    /**
     * @return string
     */
    public function getTagName() {
        return $this->tagName;
    }

    public function appendChild(Element $element) {
        $this->stream->insertBeforeElement($this, $element);
    }

    public function dumpValue() {
        return $this->tagName;
    }
}
