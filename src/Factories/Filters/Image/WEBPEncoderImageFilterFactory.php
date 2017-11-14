<?php

namespace Kibo\Phast\Factories\Filters\Image;

use Kibo\Phast\Filters\Image\WEBPEncoderImageFilter;

class WEBPEncoderImageFilterFactory {

    public function make(array $config) {
        return new WEBPEncoderImageFilter(
            $config['images']['filters'][WEBPEncoderImageFilter::class]
        );
    }

}
