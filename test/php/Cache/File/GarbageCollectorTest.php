<?php

namespace Kibo\Phast\Cache\File;

class GarbageCollectorTest extends ProbabilisticExecutorTestCase {
    public function setUp() {
        parent::setUp();
        $this->config['garbageCollection']['probability'] = 1;
        $this->config['garbageCollection']['maxAge'] = 60;
    }

    public function testGarbageCollection() {
        $results = $this->executeCacheTest();

        $dir1 = $results['ab'];
        $this->assertCount(2, $dir1);
        $this->assertContains($this->getCacheFilename('item1'), $dir1);
        $this->assertContains('a-dir', $dir1);
        $this->assertNotContains($this->getCacheFilename('item2'), $dir1);
        $this->assertNotContains($this->getCacheFilename('item3'), $dir1);
        $this->assertNotContains($this->getCacheFilename('item4'), $dir1);

        $dir2 = $results['cd'];
        $this->assertCount(3, $dir2);
        $this->assertContains($this->getCacheFilename('item4'), $dir2);
        $this->assertContains($this->getCacheFilename('item5'), $dir2);
        $this->assertContains('a-dir', $dir2);
        $this->assertNotContains($this->getCacheFilename('item6'), $dir2);
        $this->assertNotContains($this->getCacheFilename('item7'), $dir2);
    }

    public function testGarbageCollectionLimit() {
        $this->config['garbageCollection']['maxItems'] = 2;
        $result = $this->executeCacheTest();

        $totalCount = count($result['ab']) + count($result['cd']);
        $this->assertEquals(7, $totalCount);

        $dir1 = $result['ab'];
        $this->assertContains($this->getCacheFilename('item1'), $dir1);
        $this->assertContains('a-dir', $dir1);

        $dir2 = $result['cd'];
        $this->assertContains($this->getCacheFilename('item4'), $dir2);
        $this->assertContains($this->getCacheFilename('item5'), $dir2);
        $this->assertContains('a-dir', $dir2);
    }

    public function testCollectingInDeepSharding() {
        $this->config['shardingDepth'] = 3;
        $filename = $this->config['cacheRoot'] . '/ab/cd/ef/' . md5('key') . '-ns';
        mkdir(dirname($filename), 0700, true);
        touch($filename, time() - $this->config['garbageCollection']['maxAge'] - 10);
        $this->makeExecutor();
        $this->assertFileNotExists($filename);
    }

    private function executeCacheTest() {
        $this->setUpCacheContents();
        $this->makeExecutor();
        return $this->getCacheContents();
    }

    protected function setUpCacheContents() {
        $items = [
            'ab' => [
                $this->getCacheFilename('item1') => 30,
                $this->getCacheFilename('item2') => 100,
                $this->getCacheFilename('item3') => 200,
            ],
            'cd' => [
                $this->getCacheFilename('item4') => 40,
                $this->getCacheFilename('item5') => 45,
                $this->getCacheFilename('item6') => 90,
                $this->getCacheFilename('item7') => 70,
            ],
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

        mkdir($this->config['cacheRoot'] . '/nope');
        chmod($this->config['cacheRoot'] . '/nope', 0);
    }

    protected function getCacheContents() {
        $getDirItems = function ($path) {
            $entries = @scandir($path);
            if (!$entries) {
                $entries = [];
            }
            return array_filter($entries, function ($item) {
                return !in_array($item, ['.', '..']);
            });
        };
        $dirs = $getDirItems($this->config['cacheRoot']);
        $results = [];
        foreach ($dirs as $dir) {
            $path = $this->config['cacheRoot'] . '/' . $dir;
            $results[$dir] = $getDirItems($path);
        }

        $this->assertCount(3, $results);
        $this->assertArrayHasKey('ab', $results);
        $this->assertArrayHasKey('cd', $results);
        $this->assertArrayHasKey('nope', $results);
        return $results;
    }

    protected function makeExecutor() {
        return new GarbageCollector($this->config, $this->functions);
    }

    protected function setProbability($probability) {
        $this->config['garbageCollection']['probability'] = $probability;
    }

    private function getCacheFilename($key) {
        return md5($key) . '-ns';
    }
}
