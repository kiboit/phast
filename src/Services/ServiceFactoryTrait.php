<?php


namespace Kibo\Phast\Services;

use Kibo\Phast\Cache\File\Cache;
use Kibo\Phast\Filters\Service\CachingServiceFilter;
use Kibo\Phast\Filters\Service\CompositeFilter;
use Kibo\Phast\Filters\Service\Compression\CompressingFilter;
use Kibo\Phast\Filters\Service\Compression\DecompressingFilter;
use Kibo\Phast\Retrievers\CachingRetriever;
use Kibo\Phast\Retrievers\LocalRetriever;
use Kibo\Phast\Retrievers\RemoteRetrieverFactory;
use Kibo\Phast\Retrievers\UniversalRetriever;

trait ServiceFactoryTrait {
    /**
     * @param array $config
     * @param $cacheNamespace
     * @return UniversalRetriever
     */
    public function makeUniversalCachingRetriever(array $config, $cacheNamespace) {
        $retriever = new UniversalRetriever();
        $retriever->addRetriever(new LocalRetriever($config['retrieverMap']));
        $retriever->addRetriever(
            new CachingRetriever(
                new Cache($config['cache'], $cacheNamespace),
                (new RemoteRetrieverFactory())->make($config)
            )
        );
        return $retriever;
    }

    public function makeCachingServiceFilter(array $config, CompositeFilter $compositeFilter, $cacheNamespace) {
        return new CachingServiceFilter(
            new Cache($config['cache'], $cacheNamespace),
            $compositeFilter,
            new LocalRetriever($config['retrieverMap'])
        );
    }

    public function makeCachingServiceFilterWithCompression($config, CompositeFilter $compositeFilter, $cacheNamespace) {
        $compositeFilter->addFilter(new CompressingFilter());
        $caching = $this->makeCachingServiceFilter($config, $compositeFilter, $cacheNamespace);
        $wrapperComposite = new CompositeFilter();
        $wrapperComposite->addFilter($caching);
        $wrapperComposite->addFilter(new DecompressingFilter());
        return $wrapperComposite;
    }
}
