<?php


namespace Kibo\Phast\Parsing\HTML\HTMLStreamElements;


class Comment extends Element {

    public function isIEConditional() {
        return substr($this->originalString, 4, 4) === '[if ';
    }

}
