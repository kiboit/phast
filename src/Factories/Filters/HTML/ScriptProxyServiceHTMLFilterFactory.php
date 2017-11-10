<?php

namespace Kibo\Phast\Factories\Filters\HTML;

use Kibo\Phast\Filters\HTML\ScriptProxyServiceHTMLFilter;
use Kibo\Phast\ValueObjects\URL;

class ScriptProxyServiceHTMLFilterFactory implements HTMLFilterFactory {

    public function make(array $config) {
        if (!isset ($config['documents']['filters'][ScriptProxyServiceHTMLFilter::class]['serviceUrl'])) {
            $config['documents']['filters'][ScriptProxyServiceHTMLFilter::class]['serviceUrl']
            = $config['servicesUrl'] . '?service=proxy';
        }
        return new ScriptProxyServiceHTMLFilter(
            URL::fromString($config['documents']['baseUrl']),
            $config['documents']['filters'][ScriptProxyServiceHTMLFilter::class]
        );
    }

}
