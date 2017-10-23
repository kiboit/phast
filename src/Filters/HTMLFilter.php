<?php

namespace Kibo\Phast\Filters;

interface HTMLFilter {

    /**
     * @param \DOMDocument $document
     * @return null
     */
    public function transformHTMLDOM(\DOMDocument $document);

}
