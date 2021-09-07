<?php

namespace Kibo\Phast\Security;

use Kibo\Phast\Cache\File\Cache;

class ServiceSignatureFactory {
    const CACHE_NAMESPACE = 'signature';

    public function make(array $config) {
        $cache = new Cache($config['cache'], self::CACHE_NAMESPACE);
        $signature = new ServiceSignature($cache);
        if (isset($config['securityToken'])) {
            $signature->setIdentities($config['securityToken']);
        }
        return $signature;
    }
}
