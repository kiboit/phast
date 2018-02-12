<?php


namespace Kibo\Phast\Parsing\HTML\HTMLStreamElements;


class TextContainingTag implements Element {

    /**
     * @var OpeningTag
     */
    private $openingTag;

    /**
     * @var Text
     */
    private $text;

    /**
     * @var ClosingTag
     */
    private $closingTag;

    /**
     * TextContainingTag constructor.
     * @param OpeningTag $openingTag
     * @param Text $text
     * @param ClosingTag $closingTag
     */
    public function __construct(OpeningTag $openingTag, Text $text, ClosingTag $closingTag) {
        $this->openingTag = $openingTag;
        $this->text = $text;
        $this->closingTag = $closingTag;
    }

    public function getTagName() {
        return $this->openingTag->getTagName();
    }

    public function hasAttribute($attr) {
        return $this->openingTag->hasAttribute($attr);
    }

    public function getAttribute($attr) {
        return $this->openingTag->getAttribute($attr);
    }

    public function getStartStreamOffset() {
        return $this->openingTag->getStartStreamOffset();
    }

    public function getEndStreamOffset() {
        return $this->closingTag->getEndStreamOffset();
    }

    public function getTextContent() {
        return $this->text->getText();
    }

    public function output() {
        // TODO: Implement output() method.
    }


}
