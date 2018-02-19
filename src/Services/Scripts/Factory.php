<?php

namespace Kibo\Phast\Services\Scripts;

use Kibo\Phast\Cache\File\Cache;
use Kibo\Phast\Filters\HTML\ScriptsProxyService\Filter;
use Kibo\Phast\Filters\JavaScript\Minification\JSMinifierFilter;
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
                new Cache($config['cache'], 'scripts'),
                new RemoteRetriever(),
                7200
            )
        );
        $filter = new CachingServiceFilter(
            new Cache($config['cache'], 'scripts-minified'),
            new JSMinifierFilter(@$config['scripts']['removeLicenseHeaders'])
        );
        return new Service(
            (new ServiceSignatureFactory())->make($config),
            $config['documents']['filters'][Filter::class]['match'],
            $retriever,
            $filter,
            $config
        );
    }

}
