<?php

namespace Kibo\Phast\Filters\HTML\ImagesOptimizationService\Tags;

use Kibo\Phast\Filters\HTML\HTMLFilterFactory;
use Kibo\Phast\Filters\HTML\ImagesOptimizationService\ImageURLRewriterFactory;

class Factory implements HTMLFilterFactory {
    public function make(array $config) {
        return new Filter(
            (new ImageURLRewriterFactory())->make($config, Filter::class)
        );
    }
}
