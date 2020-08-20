<?php

namespace Kibo\Phast\Filters\HTML\ScriptsProxyService;

use Kibo\Phast\Filters\HTML\HTMLFilterFactory;
use Kibo\Phast\Retrievers\LocalRetriever;
use Kibo\Phast\Security\ServiceSignatureFactory;
use Kibo\Phast\Services\Bundler\TokenRefMakerFactory;

class Factory implements HTMLFilterFactory {
    public function make(array $config) {
        if (!isset($config['documents']['filters'][Filter::class]['serviceUrl'])) {
            $config['documents']['filters'][Filter::class]['serviceUrl'] = $config['servicesUrl'];
        }
        $filterConfig = $config['documents']['filters'][Filter::class];
        $filterConfig['match'] = $config['scripts']['whitelist'];
        return new Filter(
            $filterConfig,
            (new ServiceSignatureFactory())->make($config),
            new LocalRetriever($config['retrieverMap']),
            (new TokenRefMakerFactory())->make($config)
        );
    }
}
