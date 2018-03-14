<?php

namespace Kibo\Phast\Cache\File;

use Kibo\Phast\Common\ObjectifiedFunctions;
use PHPUnit\Framework\TestCase;

class CacheTest extends TestCase {

    /**
     * @var array
     */
    private $config;

    /**
     * @var ObjectifiedFunctions
     */
    private $functions;

    private $nameSpace = 'test';

    /**
     * @var Cache
     */
    private $cache;

    public function setUp() {
        parent::setUp();
        $this->config = [
            'cacheRoot' => sys_get_temp_dir() . '/test-cache-dir-' . posix_geteuid(),
            'garbageCollection' => [
                'probability' => 0,
                'maxItems' => 100,
                'maxAge' => 20
            ]
        ];
        $this->rmDir($this->config['cacheRoot']);
        $this->functions = new ObjectifiedFunctions();
        $this->functions->register_shutdown_function = function () {};
        $this->functions->error_log = function () {};
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
        $this->assertEquals("\xff", $this->cache->get('bin', function () {}));
    }

    public function testCorrectStorage() {
        $value = 'the-pirate-cache';
        $key = 'the-key-we-have';
        $this->functions->time = function () {
            return 30;
        };
        $this->cache->get($key, function () use ($value) { return $value; }, 20);
        $expectedFilename = $this->getCacheFileName($key);
        $this->assertFileExists($expectedFilename);
        $this->assertStringStartsWith('50 ', file_get_contents($expectedFilename));
        $this->assertContains($value, file_get_contents($expectedFilename));
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
        $this->cache->get('test-key', function () { return 'test-content'; });
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


        $this->functions->filemtime = function () {
            return $this->functions->time() - round($this->config['cacheMaxAge'] / 20);
        };
        $this->cache->get('asd', function () {});
        $this->assertFalse($touched);

        $this->functions->filemtime = function () {
            return $this->functions->time() - round($this->config['cacheMaxAge'] / 10);
        };
        $this->cache->get('asd', function () {});
        $this->assertTrue($touched);

    }

    public function testGarbageCollectionRunCalculation() {
        $actualMin = $actualMax = null;
        $this->functions->mt_rand = function ($min, $max) use (&$actualMin, &$actualMax) {
            $actualMin = $min;
            $actualMax = $max;
        };
        $this->functions->file_exists = function () {
            return true;
        };
        $this->config['garbageCollection']['probability'] = 0.5;
        $this->rebuildCache();
        $this->assertEquals(1, $actualMin);
        $this->assertEquals(2, $actualMax);
    }

    public function testNotExplodingWhenGarbageCollectingOnMissingCacheRoot() {
        $this->config['garbageCollection']['probability'] = 1;
        $this->rebuildCache();
    }

    public function testGarbageCollection() {
        $results = $this->executeCacheTest();

        $dir1 = $results['dir1'];
        $this->assertCount(2, $dir1);
        $this->assertContains('item1', $dir1);
        $this->assertContains('a-dir', $dir1);
        $this->assertNotContains('item2', $dir1);
        $this->assertNotContains('item3', $dir1);
        $this->assertNotContains('item4', $dir1);

        $dir2 = $results['dir2'];
        $this->assertCount(3, $dir2);
        $this->assertContains('item4', $dir2);
        $this->assertContains('item5', $dir2);
        $this->assertContains('a-dir', $dir2);
        $this->assertNotContains('item6', $dir2);
        $this->assertNotContains('item7', $dir2);
    }

    public function testGarbageCollectionLimit() {
        $this->config['garbageCollection']['maxItems'] = 2;
        $result = $this->executeCacheTest();

        $totalCount = count($result['dir1']) + count($result['dir2']);
        $this->assertEquals(7, $totalCount);

        $dir1 = $result['dir1'];
        $this->assertContains('item1', $dir1);
        $this->assertContains('a-dir', $dir1);

        $dir2 = $result['dir2'];
        $this->assertContains('item4', $dir2);
        $this->assertContains('item5', $dir2);
        $this->assertContains('a-dir', $dir2);
    }

    private function executeCacheTest() {
        $this->config['garbageCollection']['probability'] = 1;
        $this->config['garbageCollection']['maxAge'] = 60;
        $items = [
            'dir1' => [
                'item1' => 30,
                'item2' => 100,
                'item3' => 200
            ],
            'dir2' => [
                'item4' => 40,
                'item5' => 45,
                'item6' => 90,
                'item7' => 70
            ]
        ];

        foreach ($items as $dir => $files) {
            $path = $this->config['cacheRoot'] . '/' . $dir;
            mkdir($path, 0700, true);
            mkdir($path . '/a-dir', 0700);
            foreach ($files as $file => $time) {
                $filename = $path . '/' . $file;
                touch($filename, time() - $time);
            }
        }

        $this->rebuildCache();

        $getDirItems = function ($path) {
            return array_filter(scandir($path), function ($item) {
                return !in_array($item, ['.', '..']);
            });
        };
        $dirs = $getDirItems($this->config['cacheRoot']);
        $results = [];
        foreach ($dirs as $dir) {
            $path = $this->config['cacheRoot'] . '/' . $dir;
            $results[$dir] = $getDirItems($path);
        }

        $this->assertCount(2, $results);
        $this->assertArrayHasKey('dir1', $results);
        $this->assertArrayHasKey('dir2', $results);

        return $results;
    }

    private function rmDir($dir) {
        if (!file_exists($dir)) {
            return;
        }
        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }
            $fullName = $dir . '/' . $item;
            if (is_dir($fullName)) {
                $this->rmDir($fullName);
            } else {
                unlink($fullName);
            }
        }
        rmdir($dir);
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
        return join('/', [$this->config['cacheRoot'], $this->nameSpace, substr($hashedKey, 0, 2), $hashedKey]);
    }
}
