<?php

namespace Kibo\Phast\Filters\Image\Compression;

class Factory {

    public function make(array $config) {
        return new Filter($config['images']['filters'][\Kibo\Phast\Filters\Image\Compression\Filter::class]);
    }

}
