<?php

namespace Kibo\Phast\Factories\Filters\HTML;

use Kibo\Phast\Filters\HTML\CSSInliningHTMLFilter;
use Kibo\Phast\Retrievers\LocalRetriever;
use Kibo\Phast\ValueObjects\URL;

class CSSInliningHTMLFilterFactory implements HTMLFilterFactory {

    public function make(array $config) {
        return new CSSInliningHTMLFilter(
            URL::fromString($config['documents']['baseUrl']),
            new LocalRetriever($config['retrieverMap'])
        );
    }

}
