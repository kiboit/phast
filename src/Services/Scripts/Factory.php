<?php

namespace Kibo\Phast\Services\Scripts;

use Kibo\Phast\Cache\File\Cache;
use Kibo\Phast\Filters\HTML\ScriptsProxyService\Filter;
use Kibo\Phast\Filters\JavaScript\Minification\JSMinifierFilter;
use Kibo\Phast\Filters\Service\CachingServiceFilter;
use Kibo\Phast\Filters\Service\CompositeFilter;
use Kibo\Phast\Filters\Service\Compression\CompressingFilter;
use Kibo\Phast\Filters\Service\Compression\DecompressingFilter;
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
                new RemoteRetriever()
            )
        );

        $cachedComposite = new CompositeFilter();
        $cachedComposite->addFilter(new JSMinifierFilter(@$config['scripts']['removeLicenseHeaders']));
        $cachedComposite->addFilter(new CompressingFilter());

        $cachingFilter = new CachingServiceFilter(
            new Cache($config['cache'], 'scripts-minified'),
            $cachedComposite,
            new LocalRetriever($config['retrieverMap'])
        );

        $composite = new CompositeFilter();
        $composite->addFilter($cachingFilter);
        $composite->addFilter(new DecompressingFilter());

        return new Service(
            (new ServiceSignatureFactory())->make($config),
            $config['documents']['filters'][Filter::class]['match'],
            $retriever,
            $composite,
            $config
        );
    }

}
