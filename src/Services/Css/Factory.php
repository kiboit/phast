<?php

namespace Kibo\Phast\Services\Css;

use Kibo\Phast\Filters\CSS\Composite\Factory as CSSCompositeFilterFactory;
use Kibo\Phast\Security\ServiceSignatureFactory;
use Kibo\Phast\Services\ServiceFactoryTrait;


class Factory {
    use ServiceFactoryTrait;

    public function make(array $config) {
        $cssComposite = (new CSSCompositeFilterFactory())->make($config);
        $composite = $this->makeCachingServiceFilterWithCompression($config, $cssComposite, 'css-processing-2');

        return new Service(
            (new ServiceSignatureFactory())->make($config),
            [],
            $this->makeUniversalCachingRetriever($config, 'css'),
            $composite,
            $config
        );
    }

}
