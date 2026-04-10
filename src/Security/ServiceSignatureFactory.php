<?php

namespace Kibo\Phast\Security;

use Kibo\Phast\Cache\Factory as CacheFactory;

class ServiceSignatureFactory {
    const CACHE_NAMESPACE = 'signature';

    public function make(array $config) {
        $cache = (new CacheFactory(array_merge($config['cache'], [
            'name' => self::CACHE_NAMESPACE,
            'maxSize' => 1024 * 1024,
        ])))->getCache(self::CACHE_NAMESPACE);
        $signature = new ServiceSignature($cache);
        if (isset($config['securityToken'])) {
            $signature->setIdentities($config['securityToken']);
        }
        return $signature;
    }
}
