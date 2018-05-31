<?php

namespace Kibo\Phast\Services\Css;

use Kibo\Phast\Cache\File\Cache;
use Kibo\Phast\Filters\CSS\Composite\Factory as CSSCompositeFilterFactory;
use Kibo\Phast\Filters\Service\CachingServiceFilter;
use Kibo\Phast\Filters\Service\CompositeFilter;
use Kibo\Phast\Filters\Service\Compression\CompressingFilter;
use Kibo\Phast\Filters\Service\Compression\DecompressingFilter;
use Kibo\Phast\Retrievers\CachingRetriever;
use Kibo\Phast\Retrievers\LocalRetriever;
use Kibo\Phast\Retrievers\RemoteRetrieverFactory;
use Kibo\Phast\Retrievers\UniversalRetriever;
use Kibo\Phast\Security\ServiceSignatureFactory;


class Factory {

    public function make(array $config) {
        $retriever = new UniversalRetriever();
        $retriever->addRetriever(new LocalRetriever($config['retrieverMap']));
        $retriever->addRetriever(
            new CachingRetriever(
                new Cache($config['cache'], 'css'),
                (new RemoteRetrieverFactory())->make($config)
            )
        );

        $cssComposite = (new CSSCompositeFilterFactory())->make($config);
        $cssComposite->addFilter(new CompressingFilter());

        $caching = new CachingServiceFilter(
            new Cache($config['cache'], 'css-processing-2'),
            $cssComposite,
            new LocalRetriever($config['retrieverMap'])
        );

        $composite = new CompositeFilter();
        $composite->addFilter($caching);
        $composite->addFilter(new DecompressingFilter());

        return new Service(
            (new ServiceSignatureFactory())->make($config),
            [],
            $retriever,
            $composite,
            $config
        );
    }

}
