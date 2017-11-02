<?php

namespace Kibo\Phast\Factories\Services;

use Kibo\Phast\Cache\FileCache;
use Kibo\Phast\Factories\Security\ServiceSignatureFactory;
use Kibo\Phast\Services\ScriptsProxyService;

class ScriptsProxyServiceFactory {

    public static function make(array $config) {
        return new ScriptsProxyService(
            (new ServiceSignatureFactory())->make($config),
            new FileCache($config['cache'], 'scripts')
        );
    }

}
