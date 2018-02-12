<?php


namespace Kibo\Phast\Parsing\HTML\HTMLStreamElements;

class ClosingTag extends Tag {

    public function appendChild(Element $element) {
        $this->stream->insertBeforeElement($this, $element);
    }

}
