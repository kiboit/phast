<?php

namespace Kibo\Phast\Factories\Filters\Image;

use Kibo\Phast\Cache\FileCache;
use Kibo\Phast\Filters\Image\CachedCompositeImageFilter;
use Kibo\Phast\Filters\Image\CompositeImageFilter;

class CompositeImageFilterFactory {

    /**
     * @var array
     */
    private $config;

    /**
     * CompositeImageFilterFactory constructor.
     *
     * @param array $config
     */
    public function __construct(array $config) {
        $this->config = $config;
    }

    public function make(array $request) {
        if ($this->config['images']['enable-cache']) {
            $composite = new CachedCompositeImageFilter(
                new FileCache($this->config['cache']['cacheRoot'], 'images', $this->config['cache']['cacheTTL']),
                $request
            );
        } else {
            $composite = new CompositeImageFilter();
        }
        foreach (array_keys($this->config['images']['filters']) as $class) {
            $filter = $this->makeFactory($class)->make($this->config, $request);
            $composite->addImageFilter($filter);
        }
        return $composite;
    }

    private function makeFactory($filter) {
        $factory = str_replace('\Filters\\', '\Factories\Filters\\', $filter) . 'Factory';
        return new $factory();
    }

}
