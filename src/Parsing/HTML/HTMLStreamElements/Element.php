<?php


namespace Kibo\Phast\Parsing\HTML\HTMLStreamElements;

use Kibo\Phast\Common\JSON;

class Element {
    /**
     * @var string
     */
    public $originalString;

    /**
     * @param string $originalString
     */
    public function setOriginalString($originalString) {
        $this->originalString = $originalString;
    }

    public function __get($name) {
        $method = 'get' . ucfirst($name);
        if (method_exists($this, $method)) {
            return call_user_func([$this, $method]);
        }
    }

    public function __set($name, $value) {
        $method = 'set' . ucfirst($name);
        if (method_exists($this, $method)) {
            return call_user_func([$this, $method], $value);
        }
    }

    public function toString() {
        return $this->__toString();
    }

    public function __toString() {
        return isset($this->originalString) ? $this->originalString : '';
    }

    public function dump() {
        return '<' . preg_replace('~^.*\\\\~', '', get_class($this)) . ' ' . $this->dumpValue() . '>';
    }

    public function dumpValue() {
        return JSON::encode($this->originalString);
    }
}
