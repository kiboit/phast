<?php


namespace Kibo\Phast\Parsing\HTML\HTMLStreamElements;


use Kibo\Phast\Parsing\HTML\HTMLStream;

class TextContainingTag extends Element {

    /**
     * @var OpeningTag
     */
    private $openingTag;

    /**
     * @var ClosingTag
     */
    private $closingTag;

    /**
     * @var Text
     */
    private $text = '';

    /**
     * TextContainingTag constructor.
     * @param OpeningTag $openingTag
     * @param ClosingTag $closingTag
     * @param Text $text
     */
    public function __construct(OpeningTag $openingTag, ClosingTag $closingTag, Text $text = null) {
        $this->openingTag = $openingTag;
        $this->closingTag = $closingTag;
        $this->text = $text;
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

    public function setAttribute($attr, $value) {
        $this->openingTag->setAttribute($attr, $value);
    }

    public function getTextContent() {
        return isset ($this->text) ? $this->text->getText() : '';
    }

}
