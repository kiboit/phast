<?php

namespace Kibo\Phast\Cache;

interface Cache {
    /**
     * @param string $key
     * @param callable|null $cached
     * @param int $expiresIn
     * @return mixed
     */
    public function get($key, callable $cached = null, $expiresIn = 0);

    /**
     * @param string $key
     * @param mixed $value
     * @param int $expiresIn
     * @return mixed
     */
    public function set($key, $value, $expiresIn = 0);
}
