<?php


namespace Kibo\Phast\Parsing\HTML\HTMLStreamElements;

class Tag extends Element {

    /**
     * @var string
     */
    private $tagName;

    /**
     * @var array
     */
    private $attributes;

    /**
     * @var string
     */
    private $textContent = '';

    /**
     * @var string
     */
    private $closingTag;

    /**
     * Tag constructor.
     * @param $tagName
     * @param array $attributes
     */
    public function __construct($tagName, array $attributes = []) {
        $this->tagName = $tagName;
        $this->attributes = $attributes;
    }

    /**
     * @return string
     */
    public function getTagName() {
        return $this->tagName;
    }

    /**
     * @param string $attrName
     * @return bool
     */
    public function hasAttribute($attrName) {
        return isset ($this->attributes[$attrName]);
    }

    /**
     * @param string $attrName
     * @return mixed|null
     */
    public function getAttribute($attrName) {
        return $this->hasAttribute($attrName) ? $this->attributes[$attrName] : null;
    }

    /**
     * @param string $attrName
     * @param string $value
     */
    public function setAttribute($attrName, $value) {
        $this->attributes[$attrName] = $value;
    }

    /**
     * @param string $attr
     */
    public function removeAttribute($attr) {
        if ($this->hasAttribute($attr)) {
            unset ($this->attributes[$attr]);
        }
    }

    /**
     * @return string
     */
    public function getTextContent() {
        return $this->textContent;
    }

    /**
     * @param string $textContent
     */
    public function setTextContent($textContent) {
        $this->textContent = $textContent;
    }

    /**
     * @param $closingTag
     * @return Tag
     */
    public function withClosingTag($closingTag) {
        $new = clone $this;
        $new->closingTag = $closingTag;
        return $new;
    }

    /**
     * @return string
     */
    public function getClosingTag() {
        return $this->closingTag;
    }
}
