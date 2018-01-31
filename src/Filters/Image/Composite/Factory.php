<?php

namespace Kibo\Phast\Filters\Image\Composite;

use Kibo\Phast\Cache\File\Cache;
use Kibo\Phast\Environment\Package;
use Kibo\Phast\Filters\Image\ImageFactory;
use Kibo\Phast\Filters\Service\CachingServiceFilter;

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
        $composite = new Filter(new ImageFactory($this->config));
        foreach (array_keys($this->config['images']['filters']) as $class) {
            $package = Package::fromPackageClass($class);
            $filter = $package->getFactory()->make($this->config);
            $composite->addImageFilter($filter);
        }
        if ($this->config['images']['enable-cache']) {
            return new CachingServiceFilter(
                new Cache($this->config['cache'], 'images-1'),
                $composite
            );
        }
        return $composite;
    }

}
