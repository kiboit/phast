<?php

namespace Kibo\Phast\Filters\HTML\Diagnostics;

use Kibo\Phast\Filters\HTML\HTMLFilterFactory;

class Factory implements HTMLFilterFactory {
    public function make(array $config) {
        $url =  isset($config['documents']['filters'][Filter::class]['serviceUrl'])
                ? $config['documents']['filters'][Filter::class]['serviceUrl']
                : $config['servicesUrl'] . '?service=diagnostics';
        return new Filter($url);
    }
}
