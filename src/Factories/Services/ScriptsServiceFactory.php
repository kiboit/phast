<?php

namespace Kibo\Phast\Factories\Services;

use Kibo\Phast\Retrievers\UniversalRetriever;
use Kibo\Phast\Retrievers\LocalRetriever;
use Kibo\Phast\Retrievers\CachingRetriever;
use Kibo\Phast\Cache\FileCache;
use Kibo\Phast\Retrievers\RemoteRetriever;
use Kibo\Phast\Security\ServiceSignatureFactory;
use Kibo\Phast\Filters\HTML\ScriptsProxyService\Filter;
use Kibo\Phast\Services\ScriptsProxyService;

class ScriptsServiceFactory {

    public function make(array $config) {
        $retriever = new UniversalRetriever();
        $retriever->addRetriever(new LocalRetriever($config['retrieverMap']));
        $retriever->addRetriever(
            new CachingRetriever(
                new FileCache($config['cache'], 'scripts'),
                new RemoteRetriever(),
                7200
            )
        );
        return new ScriptsProxyService(
            (new ServiceSignatureFactory())->make($config),
            $config['documents']['filters'][Filter::class]['match'],
            $retriever,
            $config['scripts']['removeLicenseHeaders']
        );
    }

}
