<?php

namespace Kibo\Phast\Filters\HTML\ScriptsProxyService;

use Kibo\Phast\Filters\HTML\HTMLFilterFactory;
use Kibo\Phast\ValueObjects\URL;

class Factory implements HTMLFilterFactory {

    public function make(array $config) {
        if (!isset ($config['documents']['filters'][Filter::class]['serviceUrl'])) {
            $config['documents']['filters'][Filter::class]['serviceUrl']
            = $config['servicesUrl'] . '?service=scripts';
        }
        return new Filter(
            URL::fromString($config['documents']['baseUrl']),
            $config['documents']['filters'][Filter::class]
        );
    }

}
