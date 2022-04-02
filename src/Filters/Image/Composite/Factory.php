<?php

namespace Kibo\Phast\Filters\Image\Composite;

use Kibo\Phast\Cache\Sqlite\Cache;
use Kibo\Phast\Environment\Package;
use Kibo\Phast\Exceptions\LogicException;
use Kibo\Phast\Filters\HTML\ImagesOptimizationService\ImageInliningManagerFactory;
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
        $imageFactoryClass = $this->config['images']['factory'];
        if (!class_exists($imageFactoryClass)) {
            throw new LogicException("No such class: $imageFactoryClass");
        }
        $composite = new Filter(
            new $imageFactoryClass($this->config),
            (new ImageInliningManagerFactory())->make($this->config)
        );
        foreach ($this->config['images']['filters'] as $class => $config) {
            if ($config === null) {
                continue;
            }
            $package = Package::fromPackageClass($class);
            $filter = $package->getFactory()->make($this->config);
            $composite->addImageFilter($filter);
        }
        if ($this->config['images']['enable-cache']) {
            return new CachingServiceFilter(
                new Cache($this->config['cache'], 'images-1'),
                $composite,
                new LocalRetriever($this->config['retrieverMap'])
            );
        }
        return $composite;
    }
}
