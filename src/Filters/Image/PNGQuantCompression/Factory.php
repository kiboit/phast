<?php

namespace Kibo\Phast\Filters\Image\PNGQuantCompression;

class Factory {

    public function make(array $config) {
        return new Filter($config['images']['filters'][Filter::class]);
    }

}
