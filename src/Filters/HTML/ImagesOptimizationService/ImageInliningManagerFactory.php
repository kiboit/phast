<?php


namespace Kibo\Phast\Filters\HTML\ImagesOptimizationService;

use Kibo\Phast\Cache\Factory as CacheFactory;

class ImageInliningManagerFactory {
    public function make(array $config) {
        $cache = (new CacheFactory($config['cache']))->getCache('inline-images-1');
        return new ImageInliningManager($cache, $config['images']['maxImageInliningSize']);
    }
}
