<?php

namespace Kibo\Phast\Filters\Image\Composite;

use Kibo\Phast\Cache\FileCache;
use Kibo\Phast\Common\FactoryTrait;
use Kibo\Phast\Retrievers\LocalRetriever;

class Factory {
    use FactoryTrait;

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

    public function make() {
        if ($this->config['images']['enable-cache']) {
            $retriever = new LocalRetriever($this->config['retrieverMap']);
            $composite = new CachedFilter(
                new FileCache($this->config['cache'], 'images'),
                $retriever
            );
        } else {
            $composite = new Filter();
        }
        foreach (array_keys($this->config['images']['filters']) as $class) {
            $filter = $this->makeFactory($class)->make($this->config);
            $composite->addImageFilter($filter);
        }
        return $composite;
    }

    private function makeFactory($filter) {
        $factory = $this->getFactoryClass($filter, 'Filter');
        return new $factory();
    }

}
