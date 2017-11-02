<?php

namespace Kibo\Phast\Factories\Security;

use Kibo\Phast\Cache\FileCache;
use Kibo\Phast\Security\ServiceSignature;

class ServiceSignatureFactory {

    public function make(array $config) {
        $cache = new FileCache($config['cache'], 'signature');
        $signature = new ServiceSignature($cache);
        if (isset ($config['securityToken'])) {
            $signature->setSecurityToken($config['securityToken']);
        }
        return $signature;
    }

}
