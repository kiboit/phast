<?php


namespace Kibo\Phast\Parsing\HTML\HTMLStreamElements;

use Kibo\Phast\Parsing\HTML\HTMLStream;

class Element {

    /**
     * @var Element
     */
    protected $previous;

    /**
     * @var Element
     */
    protected $next;

    /**
     * @var HTMLStream
     */
    protected $stream;

    /**
     * @var string
     */
    protected $originalString;

    /**
     * @return Element
     */
    public function getPrevious() {
        return $this->previous;
    }

    /**
     * @param Element $previous
     */
    public function setPrevious($previous) {
        $this->previous = $previous;
    }

    /**
     * @return Element
     */
    public function getNext() {
        return $this->next;
    }

    /**
     * @param Element $next
     */
    public function setNext($next) {
        $this->next = $next;
    }

    /**
     * @param HTMLStream $stream
     */
    public function setStream(HTMLStream $stream) {
        $this->stream = $stream;
    }

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
        return isset ($this->originalString) ? $this->originalString : '';
    }

}
