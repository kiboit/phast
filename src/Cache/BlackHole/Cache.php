<?php

namespace Kibo\Phast\Cache\BlackHole;

use Kibo\Phast\Cache\Cache as CacheInterface;

class Cache implements CacheInterface {
    public function __construct(array $config = [], string $namespace = '') {
    }

    public function get($key, ?callable $cached = null, $expiresIn = 0) {
        if ($cached === null) {
            return null;
        }

        return $cached();
    }

    public function set($key, $value, $expiresIn = 0) {
    }
}
