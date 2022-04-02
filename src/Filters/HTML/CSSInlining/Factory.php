<?php

namespace Kibo\Phast\Filters\HTML\CSSInlining;

use Kibo\Phast\Cache\Sqlite\Cache;
use Kibo\Phast\Filters\CSS\Composite\Factory as CSSCompositeFactory;
use Kibo\Phast\Filters\HTML\HTMLFilterFactory;
use Kibo\Phast\Retrievers\CachingRetriever;
use Kibo\Phast\Retrievers\LocalRetriever;
use Kibo\Phast\Retrievers\UniversalRetriever;
use Kibo\Phast\Security\ServiceSignatureFactory;
use Kibo\Phast\Services\Bundler\TokenRefMakerFactory;
use Kibo\Phast\ValueObjects\URL;

class Factory implements HTMLFilterFactory {
    public function make(array $config) {
        $localRetriever = new LocalRetriever($config['retrieverMap']);

        $retriever = new UniversalRetriever();
        $retriever->addRetriever($localRetriever);
        $retriever->addRetriever(
            new CachingRetriever(
                new Cache($config['cache'], 'css')
            )
        );

        if (!isset($config['documents']['filters'][Filter::class]['serviceUrl'])) {
            $config['documents']['filters'][Filter::class]['serviceUrl'] = $config['servicesUrl'];
        }

        return new Filter(
            (new ServiceSignatureFactory())->make($config),
            URL::fromString($config['documents']['baseUrl']),
            $config['documents']['filters'][Filter::class],
            $localRetriever,
            $retriever,
            new OptimizerFactory($config),
            (new CSSCompositeFactory())->make($config),
            (new TokenRefMakerFactory())->make($config),
            $config['csp']['nonce']
        );
    }
}
