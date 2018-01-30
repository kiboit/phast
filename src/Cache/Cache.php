<?php

namespace Kibo\Phast\Cache;

interface Cache {

    /**
     * @param $hashedKey
     * @param callable|null $cached
     * @param int $expiresIn
     * @return mixed
     */
    public function get($hashedKey, callable $cached = null, $expiresIn = 0);

}
