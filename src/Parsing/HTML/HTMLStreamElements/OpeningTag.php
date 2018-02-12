<?php


namespace Kibo\Phast\Parsing\HTML\HTMLStreamElements;

class OpeningTag extends Tag {

    protected $attributes;

    public function __construct($startStreamOffset, $endStreamOffset, $tagName, array $attributes) {
        parent::__construct($startStreamOffset, $endStreamOffset, $tagName);
        $this->attributes = $attributes;
    }

    public function hasAttribute($attrName) {
        return isset ($this->attributes[$attrName]);
    }

    public function getAttribute($attrName) {
        return $this->hasAttribute($attrName) ? $this->attributes[$attrName] : null;
    }



}
