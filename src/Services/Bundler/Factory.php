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

        $cssComposite = (new CSS\Composite\Factory())->make($config);

        $caching = new CachingServiceFilter(
            new Cache($config['cache'], 'css-processing-2'),
            $cssComposite,
            new LocalRetriever($config['retrieverMap'])
        );


        return new Service(
            (new ServiceSignatureFactory())->make($config),
            $retriever,
            $caching
        );
    }

}
