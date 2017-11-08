<?php

namespace Kibo\Phast\Factories\Filters\HTML;

use Kibo\Phast\Filters\HTML\CSSOptimizingHTMLFilter;

class CSSOptimizingHTMLFilterFactory implements HTMLFilterFactory {

    public function make(array $config) {
        return new CSSOptimizingHTMLFilter();
    }

}
