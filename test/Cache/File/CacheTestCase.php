<?php

namespace Kibo\Phast\Cache\File;


use Kibo\Phast\Common\ObjectifiedFunctions;
use Kibo\Phast\PhastTestCase;

class CacheTestCase extends PhastTestCase {

    /**
     * @var array
     */
    protected $config;

    /**
     * @var ObjectifiedFunctions
     */
    protected $functions;

    public function setUp() {
        parent::setUp();
        $this->config = [
            'cacheRoot' => sys_get_temp_dir() . '/test-cache-dir-' . posix_geteuid(),
            'shardingDepth' => 1,
            'garbageCollection' => [
                'probability' => 0,
                'maxItems' => 100,
                'maxAge' => 20
            ],
            'diskCleanup' => [
                'maxSize' => 10000,
                'probability' => 0,
                'portionToFree' => 0.5
            ]
        ];
        $this->rmDir($this->config['cacheRoot']);
        $this->functions = new ObjectifiedFunctions();
        $this->functions->register_shutdown_function = function () {};
        $this->functions->error_log = function () {};
    }

    protected function rmDir($dir) {
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

}
