<?php

namespace Kibo\Phast\Cache;

use PHPUnit\Framework\TestCase;

class FileCacheTest extends TestCase {

    /**
     * @var array
     */
    private $config;

    private $nameSpace = 'test';

    /**
     * @var FileCache
     */
    private $cache;

    public function setUp() {
        parent::setUp();
        $this->config = [
            'cacheRoot' => sys_get_temp_dir() . '/test-cache-dir',
            'cacheMaxAge' => 10
        ];
        $this->rmDir($this->config['cacheRoot']);
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
            $this->nameSpace
        );
    }

    private function getCacheFileName($key) {
        return join('/', [$this->config['cacheRoot'], $this->nameSpace, substr($key, 0, 2), $key]);
    }
}
