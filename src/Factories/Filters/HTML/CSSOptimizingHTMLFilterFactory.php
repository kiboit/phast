<?php

namespace Kibo\Phast\Factories\Filters\HTML;

use Kibo\Phast\Filters\HTML\CSSOptimizingHTMLFilter;
use Kibo\Phast\Retrievers\LocalRetriever;
use Kibo\Phast\ValueObjects\URL;

class CSSOptimizingHTMLFilterFactory implements HTMLFilterFactory {

    public function make(array $config) {
        return new CSSOptimizingHTMLFilter();
    }

}
