<?php

namespace Kibo\Phast\Services\Images;

use Kibo\Phast\Filters\Image\Composite\Factory as CompositeImageFilterFactory;
use Kibo\Phast\Filters\Image\ImageFactory;
use Kibo\Phast\Security\ServiceSignatureFactory;

class Factory {

    public function make(array $config) {
        return new Service(
            (new ServiceSignatureFactory())->make($config),
            $config['images']['whitelist'],
            new ImageFactory($config),
            (new CompositeImageFilterFactory($config))->make()
        );
    }

}
