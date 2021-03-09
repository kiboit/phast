<?php


namespace Kibo\Phast\Parsing\HTML\HTMLStreamElements;

use Kibo\Phast\Parsing\HTML\HTMLInfo;

class Tag extends Element {
    /**
     * @var string
     */
    private $tagName;

    /**
     * @var array
     */
    private $attributes = [];

    /**
     * @var array
     */
    private $newAttributes = [];

    /**
     * @var \Iterator
     */
    private $attributeReader;

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
     * @param array|\Traversable $attributes
     */
    public function __construct($tagName, $attributes = []) {
        $this->tagName = strtolower($tagName);
        if ($attributes instanceof \Iterator) {
            $this->attributeReader = $attributes;
        } elseif (is_array($attributes)) {
            $this->attributeReader = new \ArrayIterator($attributes);
        } else {
            throw new \InvalidArgumentException('Attributes must be array or Iterator');
        }
    }

    /**
     * @return string
     */
    public function getTagName() {
        return $this->tagName;
    }

    /**
     * @param string $class
     * @return bool
     */
    public function hasClass($class) {
        foreach ($this->getClasses() as $c) {
            if (!strcasecmp($class, $c)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return string[]
     */
    public function getClasses() {
        return array_values(array_filter(preg_split('~\s+~', $this->getAttribute('class')), 'strlen'));
    }

    /**
     * @param string $attrName
     * @return bool
     */
    public function hasAttribute($attrName) {
        return $this->getAttribute($attrName) !== null;
    }

    /**
     * @param string $attrName
     * @return mixed|null
     */
    public function getAttribute($attrName) {
        if (array_key_exists($attrName, $this->newAttributes)) {
            return $this->newAttributes[$attrName];
        }
        if (!array_key_exists($attrName, $this->attributes)) {
            $this->readUntilAttribute($attrName);
        }
        if (isset($this->attributes[$attrName])) {
            return $this->attributes[$attrName];
        }
    }

    /** @return string[] */
    public function getAttributes() {
        $this->readUntilAttribute(null);
        return array_filter($this->newAttributes + $this->attributes, function ($value) {
            return $value !== null;
        });
    }

    private function readUntilAttribute($attrName) {
        if (!$this->attributeReader) {
            return;
        }
        while ($this->attributeReader->valid()) {
            $name = strtolower($this->attributeReader->key());
            $value = $this->attributeReader->current();
            $this->attributeReader->next();
            if (!isset($this->attributes[$name])) {
                $this->attributes[$name] = $value;
            }
            if ($name == $attrName) {
                return;
            }
        }
        $this->attributeReader = null;
    }

    /**
     * @param string $attrName
     * @param string $value
     */
    public function setAttribute($attrName, $value) {
        if ($this->getAttribute($attrName) === $value) {
            return;
        }
        $this->dirty = true;
        $this->newAttributes[$attrName] = $value;
    }

    /**
     * @param string $attrName
     */
    public function removeAttribute($attrName) {
        $this->dirty = true;
        $this->newAttributes[$attrName] = null;
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

    public function __toString() {
        return $this->getOpening() . $this->textContent . $this->getClosing();
    }

    private function getOpening() {
        if ($this->dirty || !isset($this->originalString)) {
            return $this->generateOpeningTag();
        }
        return parent::__toString();
    }

    private function getClosing() {
        if ($this->closingTag) {
            return $this->closingTag;
        }
        if ($this->mustHaveClosing() && !$this->isFromParser()) {
            return '</' . $this->tagName . '>';
        }
        return '';
    }

    private function generateOpeningTag() {
        $parts = ['<' . $this->tagName];
        foreach ($this->getAttributes() as $name => $value) {
            $parts[] = $this->generateAttribute($name, $value);
        }
        return join(' ', $parts) . '>';
    }

    private function generateAttribute($name, $value) {
        $result = $name;

        if ($value != '') {
            $result .= '=' . $this->quoteAttributeValue($value);
        }

        return $result;
    }

    private function quoteAttributeValue($value) {
        if (strpos($value, '"') === false) {
            return '"' . htmlspecialchars($value) . '"';
        }
        return "'" . str_replace(
            ['&',     "'"],
            ['&amp;', '&#039;'],
            $value
        ) . "'";
    }

    private function mustHaveClosing() {
        return !HTMLInfo::isA($this->tagName, HTMLInfo::VOID_TAG);
    }

    private function isFromParser() {
        return isset($this->originalString);
    }

    public function dumpValue() {
        $o = $this->tagName;

        foreach ($this->attributes as $name => $_) {
            $o .= " $name=\"" . $this->getAttribute($name) . '"';
        }

        if ($this->textContent) {
            $o .= " content=[{$this->textContent}]";
        }

        return $o;
    }
}
