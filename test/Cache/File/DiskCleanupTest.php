<?php

namespace Kibo\Phast\Cache\File;


class DiskCleanupTest extends CacheTestCase {

    public function testDiskCleanup() {
        $this->config['shardingDepth'] = 3;
        $cache = new Cache($this->config, 'test');
        for ($i = 0; $i < 20; $i++) {
            $cache->set('key-' . $i, str_repeat('a', 1000));
        }
        $this->config['diskCleanup']['probability'] = 1;

        new DiskCleanup($this->config);

        $files = new \RecursiveDirectoryIterator($this->config['cacheRoot']);
        $sum = 0;
        /** @var \SplFileInfo $file */
        foreach (new \RecursiveIteratorIterator($files) as $file) {
            if ($file->isDir()) {
                continue;
            }
            $sum += $file->getSize();
        }
        $this->assertLessThan(5000, $sum);
        $this->assertGreaterThan(0, $sum);
    }

}
