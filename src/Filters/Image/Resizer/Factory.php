<?php

namespace Kibo\Phast\Filters\Image\Resizer;

class Factory {

    public function make(array $config) {
        $config = $config['images']['filters'][Filter::class];
        $defaultMaxWidth = $this->getFromConfig($config, 'defaultMaxWidth');
        $defaultMaxHeight = $this->getFromConfig($config, 'defaultMaxHeight');
        return new Filter($defaultMaxWidth, $defaultMaxHeight);
    }

    private function getFromConfig(array $config, $key) {
        return isset ($config[$key]) ? (int)$config[$key] : 0;
    }

}
