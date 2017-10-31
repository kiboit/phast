<?php

namespace Kibo\Phast\Cache;

use Kibo\Phast\Common\ObjectifiedFunctions;
use PHPUnit\Framework\TestCase;

class FileCacheTest extends TestCase {

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
     * @var FileCache
     */
    private $cache;

    public function setUp() {
        parent::setUp();
        $this->config = [
            'cacheRoot' => sys_get_temp_dir() . '/test-cache-dir',
            'cacheMaxAge' => 10,
            'garbageCollection' => [
                'probability' => 0,
                'maxItems' => 100
            ]
        ];
        $this->rmDir($this->config['cacheRoot']);
        $this->functions = new ObjectifiedFunctions();
        $this->functions->register_shutdown_function = function () {};
        $this->functions->error_log = function () {};
        $this->rebuildCache();
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

    public function testCorrectStorage() {
        $value = 'the-pirate-cache';
        $key = 'the-key-we-have';
        $this->cache->get($key, function () use ($value) { return $value; });
        $expectedFilename = $this->getCacheFileName($key);
        $this->assertFileExists($expectedFilename);
        $this->assertEquals(serialize($value), file_get_contents($expectedFilename));
    }

    public function testExpiration() {
        $this->config['cacheMaxAge'] = 0;
        $this->rebuildCache();

        $calledTimes = 0;
        $cached = function () use (&$calledTimes) {
            $calledTimes++;
            return $calledTimes;
        };
        $this->cache->get('test', $cached);
        $this->cache->get('test', $cached);

        $this->assertEquals(2, $calledTimes);
        $this->assertEquals(serialize(2), file_get_contents($this->getCacheFileName('test')));
    }

    public function testNotWritingToUnownedDir() {
        $testDir = __DIR__ . '/un-owned-dir';
        $this->config['cacheRoot'] = $testDir;
        $this->rebuildCache();
        $this->cache->get('test-key', function () { return 'test-content'; });
        $dirContents = scandir($testDir . '/test/te');
        $this->assertCount(3, $dirContents);
        $this->assertContains('.', $dirContents);
        $this->assertContains('..', $dirContents);
        $this->assertContains('.gitkeep', $dirContents);
    }

    public function testGarbageCollectionRunCalculation() {
        $actualMin = $actualMax = null;
        $this->functions->mt_rand = function ($min, $max) use (&$actualMin, &$actualMax) {
            $actualMin = $min;
            $actualMax = $max;
        };
        $this->config['garbageCollection']['probability'] = 0.5;
        $this->rebuildCache();
        $this->assertEquals(1, $actualMin);
        $this->assertEquals(2, $actualMax);
    }

    public function testGarbageCollectionDeployment() {
        $deployed = 0;
        $this->functions->register_shutdown_function = function () use (&$deployed) {
            $deployed++;
        };

        $this->config['garbageCollection']['probability'] = 1;
        $this->rebuildCache();
        $this->assertEquals(1, $deployed);

        $this->config['garbageCollection']['probability'] = 0;
        $deployed = 0;
        $this->rebuildCache();
        $this->assertEquals(0, $deployed);

        $this->config['garbageCollection']['probability'] = -1;
        $this->rebuildCache();
        $this->assertEquals(0, $deployed);

        $this->config['garbageCollection']['probability'] = 2;
        $deployed = 0;
        $this->rebuildCache();
        $this->assertEquals(1, $deployed);

        $this->config['garbageCollection']['probability'] = 0.5;
        $deployed = 0;
        for ($i = 0; $i < 100; $i++) {
            $this->rebuildCache();
        }
        $this->assertLessThan(100, $deployed);
        $this->assertGreaterThan(0, $deployed);
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
        $this->config['cacheMaxAge'] = 60;
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


        $cleanup = null;
        $this->functions->register_shutdown_function = function (callable $cb) use (&$cleanup) {
            $cleanup = $cb;
        };

        $this->rebuildCache();
        $this->assertTrue(is_callable($cleanup));
        call_user_func($cleanup);

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
        $this->cache = new FileCache(
            $this->config,
            $this->nameSpace,
            $this->functions
        );
    }

    private function getCacheFileName($key) {
        return join('/', [$this->config['cacheRoot'], $this->nameSpace, substr($key, 0, 2), $key]);
    }
}
