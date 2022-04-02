<?php
namespace Kibo\Phast\Services\Bundler;

use Kibo\Phast\Cache\Sqlite\Cache;

class TokenRefMakerFactory {
    public function make(array $config) {
        $cache = new Cache($config['cache'], 'token-refs');
        return new TokenRefMaker($cache);
    }
}
