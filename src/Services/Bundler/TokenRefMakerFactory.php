<?php
namespace Kibo\Phast\Services\Bundler;

use Kibo\Phast\Cache\Factory as CacheFactory;

class TokenRefMakerFactory {
    public function make(array $config) {
        $cache = (new CacheFactory($config['cache']))->getCache('token-refs');
        return new TokenRefMaker($cache);
    }
}
