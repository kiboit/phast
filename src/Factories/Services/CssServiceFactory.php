<?php

namespace Kibo\Phast\Factories\Services;

use Kibo\Phast\Retrievers\UniversalRetriever;
use Kibo\Phast\Retrievers\LocalRetriever;
use Kibo\Phast\Retrievers\CachingRetriever;
use Kibo\Phast\Cache\FileCache;
use Kibo\Phast\Retrievers\RemoteRetriever;
use Kibo\Phast\Factories\Security\ServiceSignatureFactory;
use Kibo\Phast\Services\CSSProxyService;


class CssServiceFactory {

    public function make(array $config) {
        $retriever = new UniversalRetriever();
        $retriever->addRetriever(new LocalRetriever($config['retrieverMap']));
        $retriever->addRetriever(
            new CachingRetriever(
                new FileCache($config['cache'], 'css'),
                new RemoteRetriever(),
                7200
            )
        );
        return new CSSProxyService(
            (new ServiceSignatureFactory())->make($config),
            $retriever
        );
    }

}
