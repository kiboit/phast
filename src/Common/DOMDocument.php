<?php

namespace Kibo\Phast\Common;

class DOMDocument extends \DOMDocument {

    private $xpath;

    public function query($query) {
        if (!isset($this->xpath)) {
            $this->xpath = new \DOMXPath($this);
        }
        return $this->xpath->query($query);
    }

}
