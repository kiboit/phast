<?php

namespace Kibo\Phast\Filters\HTML\ScriptsProxyService;

use Kibo\Phast\Filters\HTML\HTMLFilterFactory;
use Kibo\Phast\Retrievers\LocalRetriever;

class Factory implements HTMLFilterFactory {

    public function make(array $config) {
        if (!isset ($config['documents']['filters'][Filter::class]['serviceUrl'])) {
            $config['documents']['filters'][Filter::class]['serviceUrl']
            = $config['servicesUrl'] . '?service=scripts';
        }
        return new Filter(
            $config['documents']['filters'][Filter::class],
            new LocalRetriever($config['retrieverMap'])
        );
    }

}
