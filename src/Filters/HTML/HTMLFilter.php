<?php

namespace Kibo\Phast\Filters\HTML;

use Kibo\Phast\Common\DOMDocument;

interface HTMLFilter {

    /**
     * @param DOMDocument $document
     * @return null
     */
    public function transformHTMLDOM(DOMDocument $document);

}
