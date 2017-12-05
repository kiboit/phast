<?php

namespace Kibo\Phast\Filters\HTML;

interface HTMLFilter {

    /**
     * @param \Kibo\Phast\Common\DOMDocument $document
     * @return null
     */
    public function transformHTMLDOM(\Kibo\Phast\Common\DOMDocument $document);

}
