<?php

namespace Kibo\Phast\Cache\Sqlite;

use Kibo\Phast\Common\ObjectifiedFunctions;

class CacheTest extends \PHPUnit\Framework\TestCase {
    private string $cacheRoot;

    private Cache $cache;

    private ObjectifiedFunctions $functions;

    public function setUp(): void {
        parent::setUp();

        $uid = function_exists('posix_geteuid') ? posix_geteuid() : 0;

        $this->cacheRoot = sys_get_temp_dir() . '/test-cache-dir-' . $uid;

        $config = [
            'cacheRoot' => $this->cacheRoot,
            'maxSize' => 512 * 1024,
        ];

        $this->functions = new ObjectifiedFunctions();

        $this->cache = new Cache(
            $config,
            'test',
            $this->functions,
        );

        $this->cache->getManager()->setAutorecover(false);
    }

    public function testCallingWithNoDefault() {
        $shouldBeNull = $this->cache->get('key');
        $shouldBeFive = $this->cache->get('key', function () {
            return 5;
        });
        $shouldBeFive2 = $this->cache->get('key');

        $this->assertNull($shouldBeNull);
        $this->assertEquals(5, $shouldBeFive);
        $this->assertEquals(5, $shouldBeFive2);
    }

    public function testCaching() {
        $value = 'the-pirate-cache';
        $key = 'the-key-we-have';

        $callsCount = 0;
        $cb = function () use (&$callsCount, $value) {
            $callsCount++;
            return $value;
        };
        $actual1 = $this->cache->get($key, $cb);
        $actual2 = $this->cache->get($key, $cb);

        $this->assertEquals($value, $actual1);
        $this->assertEquals($value, $actual2);
        $this->assertEquals(1, $callsCount);
    }

    public function testCachingBinaryData() {
        $this->assertEquals("\xff", $this->cache->get('bin', function () {
            return "\xff";
        }));
        $this->assertEquals("\xff", $this->cache->get('bin', function () {
        }));
    }

    public function testExpiration() {
        $calledTimes = 0;
        $cached = function () use (&$calledTimes) {
            $calledTimes++;
            return $calledTimes;
        };
        $this->functions->time = function () {
            return 10;
        };
        $this->cache->get('test', $cached, 10);
        $this->cache->get('test', $cached);

        $this->assertEquals(1, $calledTimes);
        $this->functions->time = function () {
            return 30;
        };
        $this->cache->get('test', $cached, 10);
        $this->assertEquals(2, $calledTimes);
    }

    public function testSet() {
        $this->cache->set('hello', 'world');

        $this->assertSame('world', $this->cache->get('hello', function () {
            throw new \Exception('get callback should not be called');
        }));
    }

    public function testCorruptedDatabase(): void {
        $this->cache->getManager()->setAutorecover(true);

        file_put_contents($this->cacheRoot . '/cache.sqlite3', 'whoops');
        $this->assertNull($this->cache->get('testje'));

        file_put_contents($this->cacheRoot . '/cache.sqlite3-wal', 'whoops');
        file_put_contents($this->cacheRoot . '/cache.sqlite3-journal', 'whoops');
        $this->assertNull($this->cache->get('testje'));
    }

    public function testMaxSize(): void {
        for ($i = 0; $i < 1000; $i++) {
            $this->cache->set((string) $i, random_bytes(1024));
        }

        $present = 0;
        for ($i = 0; $i < 1000; $i++) {
            if ($this->cache->get((string) $i) !== null) {
                $present++;
            }
        }

        $this->assertNotNull($this->cache->get('999'));

        $this->assertLessThan(1000, $present);
    }

    public function testDeflate(): void {
        $data = str_repeat('abc', 1000);
        $this->cache->set('big_data', $data);
        $this->assertSame($data, $this->cache->get('big_data'));

        $data = file_get_contents($this->cacheRoot . '/cache.sqlite3');
        $this->assertStringNotContainsString('abcabc', $data);
    }

    public function testMakingSpaceInEmptyCacheCommits(): void {
        $cache = $this->makeIsolatedCache('make-space');

        // Larger than the whole cache: makeSpace() empties the table and must
        // not leave its transaction open (which would block other writers).
        $cache->set('huge', random_bytes(1024 * 1024));
        $cache->set('small', 'value');

        $this->assertSame('value', $cache->get('small'));
        $this->assertDatabaseUnlocked('make-space');
    }

    public function testRetryingWriteAfterFullDatabase(): void {
        $cache = $this->makeIsolatedCache('full');

        // Every write beyond the size limit fails with SQLITE_FULL first and
        // is retried with the same (cached) prepared statement.
        for ($i = 0; $i < 50; $i++) {
            $cache->set("key-$i", random_bytes(100 * 1024));
        }

        $this->assertNotNull($cache->get('key-49'));
    }

    public function testCacheHitDoesNotBlockOtherWriters(): void {
        $cache = $this->makeIsolatedCache('hit');
        $cache->set('hit', 'value');

        $this->assertSame('value', $cache->get('hit'));
        $this->assertDatabaseUnlocked('hit');
    }

    public function testRecoveryOnlyPurgesCorruptDatabase(): void {
        $cache = $this->makeIsolatedCache('recovery');
        $manager = $cache->getManager();
        $manager->setAutorecover(true);
        $cache->set('kept', 'value');

        $calls = 0;
        $autorecover = new \ReflectionMethod($manager, 'autorecover');
        $autorecover->setAccessible(true);
        $result = $autorecover->invoke($manager, function () use (&$calls) {
            if (!$calls++) {
                throw new \PDOException('SQLSTATE[HY000]: General error: 8 attempt to write a readonly database');
            }
            return 'retried';
        });

        $this->assertSame('retried', $result);
        $this->assertSame(2, $calls);
        $this->assertSame('value', $cache->get('kept'));
    }

    private function makeIsolatedCache(string $name): Cache {
        foreach (glob("{$this->cacheRoot}/{$name}.sqlite3*") as $file) {
            unlink($file);
        }
        $cache = new Cache([
            'cacheRoot' => $this->cacheRoot,
            'name' => $name,
            'maxSize' => 512 * 1024,
        ], 'test', $this->functions);
        $cache->getManager()->setAutorecover(false);
        return $cache;
    }

    private function assertDatabaseUnlocked(string $name): void {
        $other = new \PDO("sqlite:{$this->cacheRoot}/{$name}.sqlite3", null, null, [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_TIMEOUT => 0,
        ]);
        // Fails with "database is locked" if another connection holds a lock.
        $other->exec('BEGIN EXCLUSIVE');
        $other->exec('COMMIT');
        $this->addToAssertionCount(1);
    }
}
