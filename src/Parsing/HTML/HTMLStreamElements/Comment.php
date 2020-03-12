<?php


namespace Kibo\Phast\Parsing\HTML\HTMLStreamElements;

class Comment extends Element {
    public function isIEConditional() {
        return (bool) preg_match('/^<!--\[if\s/', $this->originalString);
    }
}
