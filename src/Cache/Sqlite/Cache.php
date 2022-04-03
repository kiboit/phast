<?php

namespace Kibo\Phast\Cache\Sqlite;

use Kibo\Phast\Cache\Cache as CacheInterface;
use Kibo\Phast\Common\ObjectifiedFunctions;

class Cache implements CacheInterface {
    private static $managers = [];

    private $cacheRoot;

    private $name;

    private $maxSize;

    private $namespace;

    private $functions;

    public function __construct(array $config, string $namespace, ObjectifiedFunctions $functions = null) {
        $this->cacheRoot = (string) $config['cacheRoot'];
        $this->name = (string) ($config['name'] ?? 'cache');
        $this->maxSize = (int) $config['maxSize'];
        $this->namespace = $namespace;
        $this->functions = $functions ?? new ObjectifiedFunctions();
    }

    public function get($key, callable $fn = null, $expiresIn = 0) {
        return $this->getManager()->get($this->getKey($key), $fn, $expiresIn, $this->functions);
    }

    private function getKey(string $key): string {
        return $this->namespace . "\0" . $key;
    }

    public function set($key, $value, $expiresIn = 0): void {
        $this->getManager()->set($this->getKey($key), $value, $expiresIn, $this->functions);
    }

    public function getManager(): Manager {
        $key = $this->cacheRoot . '/' . $this->name;
        if (!isset(self::$managers[$key])) {
            self::$managers[$key] = new Manager($this->cacheRoot, $this->name, $this->maxSize);
        }
        return self::$managers[$key];
    }
}
