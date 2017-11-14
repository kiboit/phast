<?php

namespace Kibo\Phast\Factories\Filters\Image;

use Kibo\Phast\Filters\Image\ResizerImageFilter;

class ResizerImageFilterFactory {

    public function make(array $config) {
        $config = $config['images']['filters'][ResizerImageFilter::class];
        $defaultMaxWidth = $this->getFromConfig($config, 'defaultMaxWidth');
        $defaultMaxHeight = $this->getFromConfig($config, 'defaultMaxHeight');
        return new ResizerImageFilter($defaultMaxWidth, $defaultMaxHeight);
    }

    private function getFromConfig(array $config, $key) {
        return isset ($config[$key]) ? (int)$config[$key] : 0;
    }

}
