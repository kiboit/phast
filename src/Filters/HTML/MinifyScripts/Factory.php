<?php
namespace Kibo\Phast\Filters\HTML\MinifyScripts;

use Kibo\Phast\Cache\Factory as CacheFactory;

class Factory {
    public function make(array $config) {
        return new Filter((new CacheFactory($config['cache']))->getCache('minified-inline-scripts'));
    }
}
