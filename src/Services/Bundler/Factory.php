<?php


namespace Kibo\Phast\Services\Bundler;

use Kibo\Phast\Cache\File\Cache;
use Kibo\Phast\Filters\CSS;
use Kibo\Phast\Filters\Service\CachingServiceFilter;
use Kibo\Phast\Retrievers\CachingRetriever;
use Kibo\Phast\Retrievers\LocalRetriever;
use Kibo\Phast\Retrievers\RemoteRetrieverFactory;
use Kibo\Phast\Retrievers\UniversalRetriever;
use Kibo\Phast\Security\ServiceSignatureFactory;
use Kibo\Phast\Services\ServiceFactoryTrait;

class Factory {
    use ServiceFactoryTrait;

    public function make(array $config) {
        $cssServiceFactory = new \Kibo\Phast\Services\Css\Factory();
        $jsServiceFactory = new \Kibo\Phast\Services\Scripts\Factory();

        $cssFilter = $this->makeCachingServiceFilter(
            $config,
            $cssServiceFactory->makeFilter($config),
            'bundler-css'
        );

        $jsFilter = $this->makeCachingServiceFilter(
            $config,
            $jsServiceFactory->makeFilter(),
            'bundler-js'
        );

        return new Service(
            (new ServiceSignatureFactory())->make($config),
            $cssServiceFactory->makeRetriever($config),
            $cssFilter,
            $jsServiceFactory->makeRetriever($config),
            $jsFilter
        );
    }

}
