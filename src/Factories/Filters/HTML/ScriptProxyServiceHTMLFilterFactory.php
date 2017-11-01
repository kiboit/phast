<?php

namespace Kibo\Phast\Factories\Filters\HTML;

use Kibo\Phast\Filters\HTML\ScriptProxyServiceHTMLFilter;
use Kibo\Phast\Security\ServiceSignature;
use Kibo\Phast\ValueObjects\URL;

class ScriptProxyServiceHTMLFilterFactory implements HTMLFilterFactory {

    public function make(array $config) {
        return new ScriptProxyServiceHTMLFilter(
            URL::fromString($config['documents']['baseUrl']),
            $config['documents']['filters'][ScriptProxyServiceHTMLFilter::class],
            new ServiceSignature($config['securityToken'])
        );
    }

}
