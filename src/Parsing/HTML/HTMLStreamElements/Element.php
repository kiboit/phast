<?php


namespace Kibo\Phast\Parsing\HTML\HTMLStreamElements;

use Kibo\Phast\Parsing\HTML\HTMLStream;

class Element {

    /**
     * @var HTMLStream
     */
    protected $stream;

    /**
     * @param HTMLStream $stream
     */
    public function setStream(HTMLStream $stream) {
        $this->stream = $stream;
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

}
