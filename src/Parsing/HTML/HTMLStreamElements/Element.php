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

}
