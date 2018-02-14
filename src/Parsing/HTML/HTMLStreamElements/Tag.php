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
    private $closingTag = '';

    private $dirty = false;

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
        $this->dirty = true;
        $this->attributes[$attrName] = $value;
    }

    /**
     * @param string $attr
     */
    public function removeAttribute($attr) {
        $this->dirty = true;
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

    public function toString() {
        return $this->getOpening() . $this->textContent . $this->getClosing();
    }

    private function getOpening() {
        if ($this->dirty || !isset ($this->originalString)) {
            return $this->generateOpeningTag();
        }
        return parent::toString();
    }

    private function getClosing() {
        if (empty ($this->closingTag) && !empty($this->textContent)) {
            return '</' . $this->tagName . '>';
        }
        return $this->closingTag;
    }

    private function generateOpeningTag() {
        $parts = ['<' . $this->tagName];
        foreach ($this->attributes as $name => $value) {
            $parts[] = $name . '="' . htmlspecialchars($value) . '"';
        }
        return join(' ', $parts) . '>';
    }
}
