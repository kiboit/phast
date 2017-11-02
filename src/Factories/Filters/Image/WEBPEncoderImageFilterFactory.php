<?php

namespace Kibo\Phast\Factories\Filters\Image;

use Kibo\Phast\Filters\Image\WEBPEncoderImageFilter;

class WEBPEncoderImageFilterFactory {

    public function make(array $config, array $request) {
        return new WEBPEncoderImageFilter(
            $config['images']['filters'][WEBPEncoderImageFilter::class],
            $request
        );
    }

}
