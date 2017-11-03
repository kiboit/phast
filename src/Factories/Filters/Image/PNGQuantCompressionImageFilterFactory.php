<?php

namespace Kibo\Phast\Factories\Filters\Image;

use Kibo\Phast\Filters\Image\PNGQuantCompressionImageFilter;

class PNGQuantCompressionImageFilterFactory {

    public function make(array $config) {
        return new PNGQuantCompressionImageFilter(
            $config['images']['filters'][PNGQuantCompressionImageFilter::class]
        );
    }

}
