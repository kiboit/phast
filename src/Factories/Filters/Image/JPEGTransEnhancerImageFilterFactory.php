<?php

namespace Kibo\Phast\Factories\Filters\Image;

use Kibo\Phast\Filters\Image\JPEGTransEnhancerImageFilter;

class JPEGTransEnhancerImageFilterFactory {

    public function make(array $config) {
        return new JPEGTransEnhancerImageFilter(
            $config['images']['filters'][JPEGTransEnhancerImageFilter::class]
        );
    }

}
