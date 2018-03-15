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

    public function testCollectingInDeepSharding() {
        $this->config['shardingDepth'] = 3;
        $filename = $this->config['cacheRoot'] . '/ab/cd/ef/abcdefghtij';
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
        $this->assertArrayHasKey('dir1', $results);
        $this->assertArrayHasKey('dir2', $results);
        $this->assertArrayHasKey('nope', $results);
        return $results;
    }

    protected function makeExecutor() {
        return new GarbageCollector($this->config, $this->functions);
    }

    protected function setProbability($probability) {
        $this->config['garbageCollection']['probability'] = $probability;
    }

}
