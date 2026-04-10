<?php

namespace Kibo\Phast\Cache;

use Kibo\Phast\Exceptions\LogicException;

class Factory {
    /**
     * @var array
     */
    private $config;

    public function __construct(array $config) {
        $this->config = $config;
    }

    public function getCache(string $namespace): Cache {
        $implementation = $this->config['implementation'] ?? null;
        if (!is_string($implementation) || !$implementation) {
            throw new LogicException('Cache implementation is not configured');
        }
        if (!class_exists($implementation)) {
            throw new LogicException("No such class: $implementation");
        }

        return new $implementation($this->config, $namespace);
    }
}
