<?php


namespace Kibo\Phast\Services;

use Kibo\Phast\Cache\Factory as CacheFactory;
use Kibo\Phast\Filters\Service\CachingServiceFilter;
use Kibo\Phast\Filters\Service\CompositeFilter;
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
                (new CacheFactory($config['cache']))->getCache($cacheNamespace),
                (new RemoteRetrieverFactory())->make($config)
            )
        );
        return $retriever;
    }

    public function makeCachingServiceFilter(array $config, CompositeFilter $compositeFilter, $cacheNamespace) {
        return new CachingServiceFilter(
            (new CacheFactory($config['cache']))->getCache($cacheNamespace),
            $compositeFilter,
            new LocalRetriever($config['retrieverMap'])
        );
    }
}
