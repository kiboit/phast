<?php

namespace Kibo\Phast\Security;

use Kibo\Phast\Cache\File\Cache;

class ServiceSignatureFactory {
    public function make(array $config) {
        $cache = new Cache($config['cache'], 'signature');
        $signature = new ServiceSignature($cache);
        if (isset($config['securityToken'])) {
            $signature->setIdentities($config['securityToken']);
        }
        return $signature;
    }
}
