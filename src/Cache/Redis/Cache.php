<?php

namespace Kibo\Phast\Cache\Redis;

use Kibo\Phast\Cache\Cache as CacheInterface;
use Kibo\Phast\Common\ObjectifiedFunctions;
use Kibo\Phast\Logging\LoggingTrait;

/**
 * Cache backed by Redis, using the phpredis extension.
 *
 * Configuration (under `cache`):
 *
 *     'implementation' => \Kibo\Phast\Cache\Redis\Cache::class,
 *     'redis' => [
 *         'host' => '/run/redis/redis.sock', // or a hostname
 *         'port' => 0,                       // 0 for a unix socket
 *         'database' => 0,
 *         'prefix' => 'phast:',
 *         'timeout' => 1.0,                  // connect/read timeout, seconds
 *         'defaultTtl' => 0,                 // seconds for entries without
 *                                            // an expiry; 0: none
 *     ],
 *
 * Entries without an expiry are kept until Redis evicts them, or for
 * `defaultTtl` seconds if set. Caches configured as `persistent` (such as the
 * service signature token) never get a default TTL. Either configure Redis
 * with a `maxmemory` limit and an eviction policy (with a `defaultTtl`,
 * `volatile-lru` never evicts persistent entries), or, with a disk-based
 * Redis-compatible server without eviction such as Kvrocks, use `defaultTtl`
 * to bound its size.
 *
 * Redis errors are logged and treated as cache misses: a failing cache makes
 * requests slower, never fail.
 */
class Cache implements CacheInterface {
    use LoggingTrait;

    /** @var array<string, \Redis> */
    private static $connections = [];

    private $config;

    private $namespace;

    private $persistent;

    private $functions;

    public function __construct(array $config, string $namespace, ?ObjectifiedFunctions $functions = null) {
        $this->config = ($config['redis'] ?? []) + [
            'host' => '127.0.0.1',
            'port' => 6379,
            'database' => 0,
            'prefix' => 'phast:',
            'timeout' => 1.0,
            'defaultTtl' => 0,
        ];
        $this->persistent = !empty($config['persistent']);
        $this->namespace = $namespace;
        $this->functions = $functions ?? new ObjectifiedFunctions();
    }

    public function get($key, ?callable $cached = null, $expiresIn = 0) {
        $data = $this->call(function (\Redis $redis) use ($key) {
            return $redis->get($this->getKey($key));
        });
        if (is_string($data) && $this->unserialize($data, $value)) {
            return $value;
        }
        if ($cached === null) {
            return null;
        }
        $value = $cached();
        $this->set($key, $value, $expiresIn);
        return $value;
    }

    public function set($key, $value, $expiresIn = 0) {
        $data = $this->serialize($value);
        $ttl = (int) $expiresIn;
        if ($ttl <= 0 && !$this->persistent) {
            $ttl = (int) $this->config['defaultTtl'];
        }
        $this->call(function (\Redis $redis) use ($key, $data, $ttl) {
            if ($ttl > 0) {
                return $redis->set($this->getKey($key), $data, ['ex' => $ttl]);
            }
            return $redis->set($this->getKey($key), $data);
        });
    }

    private function getKey(string $key): string {
        // Hashed: keys may be long URLs and contain any bytes.
        return $this->config['prefix'] . sha1($this->namespace . "\0" . $key, true);
    }

    private function serialize($value): string {
        // gzcompress adds a checksum, so corrupted entries are detected.
        return gzcompress(serialize($value));
    }

    private function unserialize(string $data, &$result): bool {
        $data = @gzuncompress($data);
        if ($data === false) {
            return false;
        }
        if ($data === serialize(false)) {
            $result = false;
            return true;
        }
        $value = @unserialize($data);
        if ($value === false) {
            return false;
        }
        $result = $value;
        return true;
    }

    /**
     * @return mixed The callback's result, or null if Redis failed.
     */
    private function call(callable $fn) {
        if (!extension_loaded('redis')) {
            $this->logger()->error('Redis cache error: the phpredis extension is not loaded');
            return null;
        }
        try {
            return $fn($this->connect());
        } catch (\RedisException $e) {
            $this->logger()->error('Redis cache error: {message}', ['message' => $e->getMessage()]);
            // Reconnect on the next call; the connection may be broken.
            unset(self::$connections[$this->getConnectionKey()]);
            return null;
        }
    }

    private function connect(): \Redis {
        $connectionKey = $this->getConnectionKey();
        if (!isset(self::$connections[$connectionKey])) {
            $redis = new \Redis();
            // Persistent: avoids a new connection for every request.
            if (!$redis->pconnect(
                (string) $this->config['host'],
                (int) $this->config['port'],
                (float) $this->config['timeout'],
                $connectionKey,
            )) {
                throw new \RedisException('Could not connect to Redis');
            }
            $redis->setOption(\Redis::OPT_READ_TIMEOUT, (float) $this->config['timeout']);
            if ($this->config['database']) {
                $redis->select((int) $this->config['database']);
            }
            self::$connections[$connectionKey] = $redis;
        }
        return self::$connections[$connectionKey];
    }

    private function getConnectionKey(): string {
        return 'phast:' . $this->config['host'] . ':' . $this->config['port'] . ':' . $this->config['database'];
    }
}
