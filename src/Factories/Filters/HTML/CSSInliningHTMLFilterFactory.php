<?php

namespace Kibo\Phast\Factories\Filters\HTML;

use Kibo\Phast\Filters\HTML\CSSInliningHTMLFilter;
use Kibo\Phast\Retrievers\LocalRetriever;
use Kibo\Phast\Retrievers\RemoteRetriever;
use Kibo\Phast\Retrievers\UniversalRetriever;
use Kibo\Phast\ValueObjects\URL;

class CSSInliningHTMLFilterFactory implements HTMLFilterFactory {

    public function make(array $config) {
        $retriever = new UniversalRetriever();
        $retriever->addRetriever(new LocalRetriever($config['retrieverMap']));
        $retriever->addRetriever(new RemoteRetriever());
        return new CSSInliningHTMLFilter(
            URL::fromString($config['documents']['baseUrl']),
            $config['documents']['filters'][CSSInliningHTMLFilter::class]['whitelist'],
            $retriever
        );
    }

}
