<?php


namespace Kibo\Phast\Filters\Image\ImageAPIClient;

use Kibo\Phast\Cache\Sqlite\Cache;
use Kibo\Phast\Filters\Image\ImageFilterFactory;
use Kibo\Phast\HTTP\ClientFactory;
use Kibo\Phast\Security\ServiceSignature;

class Factory implements ImageFilterFactory {
    public function make(array $config) {
        $signature = new ServiceSignature(new Cache($config['cache'], 'api-service-signature'));
        return new Filter(
            $config['images']['filters'][Filter::class],
            $signature,
            (new ClientFactory())->make($config)
        );
    }
}
