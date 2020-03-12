<?php
namespace Kibo\Phast\Services\Bundler;

use Kibo\Phast\Cache\File\Cache;

class TokenRefMakerFactory {
    public function make(array $config) {
        $cache = new Cache($config['cache'], 'token-refs');
        return new TokenRefMaker($cache);
    }
}
