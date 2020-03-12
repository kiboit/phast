<?php


namespace Kibo\Phast\Services\Bundler;

use \Kibo\Phast\Services\Css;
use \Kibo\Phast\Services\Scripts;
use Kibo\Phast\Security\ServiceSignatureFactory;
use Kibo\Phast\Services\ServiceFactoryTrait;

class Factory {
    use ServiceFactoryTrait;

    public function make(array $config) {
        $cssServiceFactory = new Css\Factory();
        $jsServiceFactory = new Scripts\Factory();

        $cssFilter = $this->makeCachingServiceFilter(
            $config,
            $cssServiceFactory->makeFilter($config),
            'bundler-css'
        );

        $jsFilter = $this->makeCachingServiceFilter(
            $config,
            $jsServiceFactory->makeFilter($config),
            'bundler-js'
        );

        return new Service(
            (new ServiceSignatureFactory())->make($config),
            $cssServiceFactory->makeRetriever($config),
            $cssFilter,
            $jsServiceFactory->makeRetriever($config),
            $jsFilter,
            (new TokenRefMakerFactory())->make($config)
        );
    }
}
