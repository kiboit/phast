<?php

namespace Kibo\Phast\Filters\HTML;

interface HTMLFilter {

    /**
     * @param \DOMDocument $document
     * @return null
     */
    public function transformHTMLDOM(\DOMDocument $document);

}
