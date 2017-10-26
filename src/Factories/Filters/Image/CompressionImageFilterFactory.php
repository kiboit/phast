<?php

namespace Kibo\Phast\Factories\Filters\Image;

use Kibo\Phast\Filters\Image\CompressionImageFilter;

class CompressionImageFilterFactory {

    public function make(array $config) {
        return new CompressionImageFilter($config);
    }

}
