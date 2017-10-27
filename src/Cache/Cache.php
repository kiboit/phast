<?php

namespace Kibo\Phast\Cache;

interface Cache {

    /**
     * @param $key
     * @param callable $cached
     * @return mixed
     */
    public function get($key, callable $cached);

}
