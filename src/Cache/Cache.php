<?php

namespace Kibo\Phast\Cache;

interface Cache {

    /**
     * @param $hashedKey
     * @param callable $cached
     * @return mixed
     */
    public function get($hashedKey, callable $cached);

}
