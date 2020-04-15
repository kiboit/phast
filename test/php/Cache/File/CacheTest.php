<?php

namespace Kibo\Phast\Cache\File;

class CacheTest extends CacheTestCase {
    private $nameSpace = 'test';

    /**
     * @var Cache
     */
    private $cache;

    public function setUp() {
        parent::setUp();
        $this->rebuildCache();
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
        $this->rebuildCache();
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

    public function testCorrectStorage() {
        $value = 'the-pirate-cache';
        $key = 'the-key-we-have';
        $this->functions->time = function () {
            return 30;
        };
        $this->cache->get($key, function () use ($value) {
            return $value;
        }, 20);
        $expectedFilename = $this->getCacheFileName($key);
        $this->assertFileExists($expectedFilename);
        $this->assertStringStartsWith('50 ', file_get_contents($expectedFilename));
        $this->assertContains($value, file_get_contents($expectedFilename));
    }

    public function testShardingDepth() {
        $this->config['shardingDepth'] = 3;
        $this->rebuildCache();
        $key = 'the-key-we-have';
        $this->cache->set($key, 'the-pirate-cache');
        $expectedFilename = $this->getCacheFileName($key);
        $this->assertFileExists($expectedFilename);
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

    public function testNotWritingToUnownedDir() {
        $this->functions->file_put_contents = function () {
            $this->fail('Cache was written');
        };
        $this->functions->posix_geteuid = function () {
            return 1000;
        };
        $this->functions->fileowner = function ($dir) {
            $this->assertEquals($this->config['cacheRoot'], $dir);
            return 2000;
        };
        $this->cache->get('test-key', function () {
            return 'test-content';
        });
    }

    public function testTouchingUsedFiles() {
        $touched = false;
        $this->functions->touch = function () use (&$touched) {
            $touched = true;
        };
        $this->functions->file_exists = function () {
            return true;
        };
        $this->functions->time = function () {
            return 1000;
        };
        $this->functions->file_get_contents = function () {
            return 'contents';
        };


        $this->functions->filectime = function () {
            return $this->functions->time() - round($this->config['cacheMaxAge'] / 20);
        };
        $this->cache->get('asd', function () {
        });
        $this->assertFalse($touched);

        $this->functions->filectime = function () {
            return $this->functions->time() - round($this->config['cacheMaxAge'] / 10);
        };
        $this->cache->get('asd', function () {
        });
        $this->assertTrue($touched);
    }

    public function testCreatingASingleGarbageCollectorForAllInstances() {
        $gc1 = $this->cache->getGarbageCollector();
        $gc2 = $this->cache->getGarbageCollector();
        $this->assertNotNull($gc1);
        $this->assertSame($gc1, $gc2);
    }

    public function testCreatingASingleDiskCleanupForAllInstances() {
        $dc1 = $this->cache->getDiskCleanup();
        $dc2 = $this->cache->getDiskCleanup();
        $this->assertNotNull($dc1);
        $this->assertSame($dc1, $dc2);
    }

    private function rebuildCache() {
        $this->cache = new Cache(
            $this->config,
            $this->nameSpace,
            $this->functions
        );
    }

    private function getCacheFileName($key) {
        $hashedKey = md5($key);
        $shardingDepth = $this->config['shardingDepth'];
        $dirs = [$this->config['cacheRoot']];
        for ($i = 0; $i < $shardingDepth * 2; $i += 2) {
            $dirs[] = substr($hashedKey, $i, 2);
        }
        return join('/', array_merge($dirs, [$hashedKey . '-' . $this->nameSpace]));
    }
}
