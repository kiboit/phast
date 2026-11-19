<?php

namespace Kibo\Phast\Cache\Redis;

use Kibo\Phast\Common\ObjectifiedFunctions;
use PHPUnit\Framework\TestCase;

/**
 * Needs the phpredis extension and a Redis server; set PHAST_TEST_REDIS_SOCKET
 * to its unix socket (or PHAST_TEST_REDIS_HOST/PHAST_TEST_REDIS_PORT).
 */
class CacheTest extends TestCase {
    private $config;

    private $functions;

    public function setUp(): void {
        parent::setUp();
        if (!extension_loaded('redis')) {
            $this->markTestSkipped('phpredis is not loaded');
        }
        if ($socket = getenv('PHAST_TEST_REDIS_SOCKET')) {
            $redis = ['host' => $socket, 'port' => 0];
        } elseif ($host = getenv('PHAST_TEST_REDIS_HOST')) {
            $redis = ['host' => $host, 'port' => (int) (getenv('PHAST_TEST_REDIS_PORT') ?: 6379)];
        } else {
            $this->markTestSkipped('No test Redis configured (PHAST_TEST_REDIS_SOCKET)');
        }
        $this->config = ['redis' => $redis + ['prefix' => 'phast-test:' . uniqid() . ':']];
        $this->functions = new ObjectifiedFunctions();
    }

    private function cache(string $namespace = 'test', ?array $config = null): Cache {
        return new Cache($config ?? $this->config, $namespace, $this->functions);
    }

    public function testCaching(): void {
        $calls = 0;
        $cb = function () use (&$calls) {
            $calls++;
            return ['some' => 'value'];
        };
        $this->assertSame(['some' => 'value'], $this->cache()->get('key', $cb));
        $this->assertSame(['some' => 'value'], $this->cache()->get('key', $cb));
        $this->assertSame(1, $calls);
    }

    public function testMissWithoutCallbackReturnsNull(): void {
        $this->assertNull($this->cache()->get('missing'));
    }

    public function testSetAndBinaryData(): void {
        $this->cache()->set('bin', "\xff\x00");
        $this->assertSame("\xff\x00", $this->cache()->get('bin'));
    }

    public function testFalseIsCached(): void {
        $calls = 0;
        $cb = function () use (&$calls) {
            $calls++;
            return false;
        };
        $this->assertFalse($this->cache()->get('false', $cb));
        $this->assertFalse($this->cache()->get('false', $cb));
        $this->assertSame(1, $calls);
    }

    public function testNamespacesAreSeparate(): void {
        $this->cache('a')->set('key', 'a');
        $this->cache('b')->set('key', 'b');
        $this->assertSame('a', $this->cache('a')->get('key'));
        $this->assertSame('b', $this->cache('b')->get('key'));
    }

    public function testExpiryIsSetInRedis(): void {
        $this->cache()->set('expiring', 'value', 100);
        $this->cache()->set('forever', 'value');

        $redis = $this->redis();
        $keys = $redis->keys($this->config['redis']['prefix'] . '*');
        $ttls = array_map([$redis, 'ttl'], $keys);
        sort($ttls);
        $this->assertCount(2, $ttls);
        $this->assertSame(-1, $ttls[0]);
        $this->assertGreaterThan(90, $ttls[1]);
        $this->assertLessThanOrEqual(100, $ttls[1]);
    }

    public function testDefaultTtlAppliesOnlyWithoutExplicitExpiry(): void {
        $config = $this->config;
        $config['redis']['defaultTtl'] = 1000;
        $this->cache('test', $config)->set('default', 'value');
        $this->cache('test', $config)->set('explicit', 'value', 100);

        $this->assertSame([100, 1000], $this->ttlsByMagnitude());
    }

    public function testPersistentCacheGetsNoDefaultTtl(): void {
        $config = $this->config;
        $config['redis']['defaultTtl'] = 1000;
        $config['persistent'] = true;
        $this->cache('test', $config)->set('token', 'value');

        $this->assertSame([-1], $this->ttlsByMagnitude());
    }

    public function testSignatureCacheIsPersistent(): void {
        $config = \Kibo\Phast\Environment\DefaultConfiguration::get();
        $config['cache'] = [
            'implementation' => Cache::class,
            'redis' => $this->config['redis'] + ['defaultTtl' => 1000],
        ];
        $signature = (new \Kibo\Phast\Security\ServiceSignatureFactory())->make($config);
        $signature->sign('something');

        $this->assertSame([-1], $this->ttlsByMagnitude());
    }

    /** TTLs of all test keys, rounded to the nearest power of ten. */
    private function ttlsByMagnitude(): array {
        $redis = $this->redis();
        $ttls = array_map(function ($key) use ($redis) {
            $ttl = $redis->ttl($key);
            return $ttl <= 0 ? $ttl : (int) (10 ** round(log10($ttl)));
        }, $redis->keys($this->config['redis']['prefix'] . '*'));
        sort($ttls);
        return $ttls;
    }

    public function testCorruptEntryIsAMiss(): void {
        $this->cache()->set('key', 'value');
        $redis = $this->redis();
        foreach ($redis->keys($this->config['redis']['prefix'] . '*') as $key) {
            $redis->set($key, 'garbage');
        }
        $this->assertSame('fresh', $this->cache()->get('key', function () {
            return 'fresh';
        }));
    }

    public function testUnreachableRedisBehavesAsMiss(): void {
        $cache = $this->cache('test', ['redis' => ['host' => sys_get_temp_dir() . '/no-such-redis.sock', 'port' => 0]]);
        $this->assertSame('computed', $cache->get('key', function () {
            return 'computed';
        }));
        $cache->set('key', 'value');
        $this->assertNull($cache->get('key'));
    }

    private function redis(): \Redis {
        $redis = new \Redis();
        $redis->connect($this->config['redis']['host'], $this->config['redis']['port']);
        return $redis;
    }
}
