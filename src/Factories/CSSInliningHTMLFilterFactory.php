<?php

namespace Kibo\Phast\Factories;

use Kibo\Phast\Filters\CSSInliningHTMLFilter;
use Kibo\Phast\Retrievers\LocalRetriever;
use Kibo\Phast\ValueObjects\URL;

class CSSInliningHTMLFilterFactory implements HTMLFilterFactory {

    public function make(array $config) {
        return new CSSInliningHTMLFilter(
            URL::fromString($config['baseUrl']),
            new LocalRetriever($config['retrieverMap'])
        );
    }

}
