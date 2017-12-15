<?php

namespace Kibo\Phast\Factories\Services;

use Kibo\Phast\Services\ImageFilteringService;
use Kibo\Phast\Factories\Security\ServiceSignatureFactory;
use Kibo\Phast\Filters\Image\ImageFactory;
use Kibo\Phast\Filters\Image\Composite\Factory;

class ImagesServiceFactory {

    public function make(array $config) {
        return new ImageFilteringService(
            (new ServiceSignatureFactory())->make($config),
            $config['images']['whitelist'],
            new ImageFactory($config),
            (new Factory($config))->make()
        );
    }

}
