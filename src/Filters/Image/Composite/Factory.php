<?php

namespace Kibo\Phast\Filters\Image\Composite;

use Kibo\Phast\Cache\File\Cache;
use Kibo\Phast\Environment\Package;
use Kibo\Phast\Filters\Service\CachingServiceFilter;
use Kibo\Phast\Retrievers\LocalRetriever;

class Factory {

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
            $composite = new CachingServiceFilter(
                new Cache($this->config['cache'], 'images'),
                $retriever
            );
        } else {
            $composite = new Filter();
        }
        foreach (array_keys($this->config['images']['filters']) as $class) {
            $package = Package::fromPackageClass($class);
            $filter = $package->getFactory()->make($this->config);
            $composite->addImageFilter($filter);
        }
        return $composite;
    }

}
