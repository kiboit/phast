<?php


namespace Kibo\Phast\Filters\HTML\ImagesOptimizationService;

use Kibo\Phast\Cache\Sqlite\Cache;

class ImageInliningManagerFactory {
    public function make(array $config) {
        $cache = new Cache($config['cache'], 'inline-images-1');
        return new ImageInliningManager($cache, $config['images']['maxImageInliningSize']);
    }
}
