<?php

namespace Kibo\Phast\Security;

use Kibo\Phast\Cache\Sqlite\Cache;

class ServiceSignatureFactory {
    const CACHE_NAMESPACE = 'signature';

    public function make(array $config) {
        $cache = new Cache(array_merge($config['cache'], [
            'name' => self::CACHE_NAMESPACE,
            'maxSize' => 1024 * 1024,
        ]), self::CACHE_NAMESPACE);
        $signature = new ServiceSignature($cache);
        if (isset($config['securityToken'])) {
            $signature->setIdentities($config['securityToken']);
        }
        return $signature;
    }
}
