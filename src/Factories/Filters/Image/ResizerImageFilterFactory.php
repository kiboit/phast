<?php

namespace Kibo\Phast\Factories\Filters\Image;

use Kibo\Phast\Filters\Image\ResizerImageFilter;

class ResizerImageFilterFactory {

    public function make(array $config, array $request) {
        $defaultMaxWidth = $this->getFromConfig($config, 'defaultMaxWidth');
        $defaultMaxHeight = $this->getFromConfig($config, 'defaultMaxHeight');
        $priorityMaxWidth = $this->getFromConfig($request, 'width');
        $priorityMaxHeight = $this->getFromConfig($request, 'height');
        return new ResizerImageFilter($defaultMaxWidth, $defaultMaxHeight, $priorityMaxWidth, $priorityMaxHeight);
    }

    private function getFromConfig(array $config, $key) {
        return isset ($config[$key]) ? (int)$config[$key] : 0;
    }

}
