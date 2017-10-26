<?php

namespace Kibo\Phast\Factories\Filters\Images;

use Kibo\Phast\Filters\Image\CompressionImageFilter;

class CompressionImageFilterFactory {

    public function make(array $config) {
        return new CompressionImageFilter($config);
    }

}
