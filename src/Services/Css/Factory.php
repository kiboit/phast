<?php

namespace Kibo\Phast\Services\Css;

use Kibo\Phast\Filters\CSS\Composite\Factory as CSSCompositeFilterFactory;
use Kibo\Phast\Security\ServiceSignatureFactory;
use Kibo\Phast\Services\ServiceFactoryTrait;

class Factory {
    use ServiceFactoryTrait;

    public function make(array $config) {
        $cssComposite = $this->makeFilter($config);
        $composite = $this->makeCachingServiceFilter($config, $cssComposite, 'css-processing-2');

        return new Service(
            (new ServiceSignatureFactory())->make($config),
            [],
            $this->makeRetriever($config),
            $composite,
            $config
        );
    }

    public function makeRetriever(array $config) {
        return $this->makeUniversalCachingRetriever($config, 'css');
    }

    public function makeFilter(array $config) {
        return (new CSSCompositeFilterFactory())->make($config);
    }
}
