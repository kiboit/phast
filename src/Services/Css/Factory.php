<?php

namespace Kibo\Phast\Services\Css;

use Kibo\Phast\Cache\File\Cache;
use Kibo\Phast\Filters\CSS\Composite\Factory as CSSCompositeFilterFactory;
use Kibo\Phast\Filters\Service\CachingServiceFilter;
use Kibo\Phast\Retrievers\CachingRetriever;
use Kibo\Phast\Retrievers\LocalRetriever;
use Kibo\Phast\Retrievers\RemoteRetriever;
use Kibo\Phast\Retrievers\UniversalRetriever;
use Kibo\Phast\Security\ServiceSignatureFactory;


class Factory {

    public function make(array $config) {
        $retriever = new UniversalRetriever();
        $retriever->addRetriever(new LocalRetriever($config['retrieverMap']));
        $retriever->addRetriever(
            new CachingRetriever(
                new Cache($config['cache'], 'css'),
                new RemoteRetriever(),
                7200
            )
        );

        $composite = (new CSSCompositeFilterFactory())->make($config);
        $caching = new CachingServiceFilter(
            new Cache($config['cache'], 'css-processing-2'),
            $composite,
            new LocalRetriever($config['retrieverMap'])
        );

        return new Service(
            (new ServiceSignatureFactory())->make($config),
            [],
            $retriever,
            $caching,
            $config
        );
    }

}
