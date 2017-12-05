<?php

namespace Kibo\Phast\Common;

class DOMDocument extends \DOMDocument {

    private $xpath;

    public function xpath($query) {
        if (!isset($this->xpath)) {
            $this->xpath = new \DOMXPath($this);
        }
        return $this->xpath->query($query);
    }

}
