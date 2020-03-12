<?php


namespace Kibo\Phast\Filters\CSS\ImageURLRewriter;

use Kibo\Phast\Filters\HTML\ImagesOptimizationService\ImageURLRewriterFactory;

class Factory {
    public function make(array $config) {
        return new Filter(
            (new ImageURLRewriterFactory())->make($config, Filter::class)
        );
    }
}
