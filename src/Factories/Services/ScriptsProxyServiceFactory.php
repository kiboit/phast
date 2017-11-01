<?php

namespace Kibo\Phast\Factories\Services;

use Kibo\Phast\Cache\FileCache;
use Kibo\Phast\Security\ServiceSignature;
use Kibo\Phast\Services\ScriptsProxyService;

class ScriptsProxyServiceFactory {

    public static function make(array $config) {
        return new ScriptsProxyService(
            new ServiceSignature($config['securityToken']),
            new FileCache($config['cache'], 'scripts')
        );
    }

}
