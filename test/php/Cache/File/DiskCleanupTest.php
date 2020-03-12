<?php

namespace Kibo\Phast\Cache\File;

class DiskCleanupTest extends ProbabilisticExecutorTestCase {
    public function testDiskCleanup() {
        $this->setUpCacheContents();
        $this->setProbability(1);
        $this->makeExecutor();
        $sum = $this->getCacheContents();
        $this->assertLessThan(5000, $sum);
        $this->assertGreaterThan(0, $sum);
    }

    protected function makeExecutor() {
        return new DiskCleanup($this->config, $this->functions);
    }

    protected function setProbability($probability) {
        $this->config['diskCleanup']['probability'] = $probability;
    }

    protected function setUpCacheContents() {
        $this->config['shardingDepth'] = 3;
        $cache = new Cache($this->config, 'test');
        for ($i = 0; $i < 20; $i++) {
            $cache->set('key-' . $i, str_repeat('a', 1000));
        }
    }

    protected function getCacheContents() {
        $files = new \RecursiveDirectoryIterator($this->config['cacheRoot']);
        $sum = 0;
        /** @var \SplFileInfo $file */
        foreach (new \RecursiveIteratorIterator($files) as $file) {
            if ($file->isDir()) {
                continue;
            }
            $sum += $file->getSize();
        }
        return $sum;
    }
}
