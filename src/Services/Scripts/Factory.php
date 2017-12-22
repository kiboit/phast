<?php

namespace Kibo\Phast\Services\Scripts;

use Kibo\Phast\Cache\File\Cache;
use Kibo\Phast\Filters\HTML\ScriptsProxyService\Filter;
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
                new Cache($config['cache'], 'scripts'),
                new RemoteRetriever(),
                7200
            )
        );
        return new Service(
            (new ServiceSignatureFactory())->make($config),
            $config['documents']['filters'][Filter::class]['match'],
            $retriever,
            $config['scripts']['removeLicenseHeaders'],
            new Cache($config['cache'], 'scripts-minified')
        );
    }

}
