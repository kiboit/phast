<?php

namespace Kibo\Phast\Factories\Filters\HTML;

use Kibo\Phast\Cache\FileCache;
use Kibo\Phast\Filters\HTML\CSSInliningHTMLFilter;
use Kibo\Phast\Retrievers\CachingRetriever;
use Kibo\Phast\Retrievers\LocalRetriever;
use Kibo\Phast\Retrievers\UniversalRetriever;
use Kibo\Phast\ValueObjects\URL;

class CSSInliningHTMLFilterFactory implements HTMLFilterFactory {

    public function make(array $config) {
        $retriever = new UniversalRetriever();
        $retriever->addRetriever(new LocalRetriever($config['retrieverMap']));
        $retriever->addRetriever(
            new CachingRetriever(
                new FileCache($config['cache'], 'css')
            )
        );

        if (!isset ($config['documents']['filters'][CSSInliningHTMLFilter::class]['serviceUrl'])) {
            $url = $config['servicesUrl'];
            $config['documents']['filters'][CSSInliningHTMLFilter::class]['serviceUrl']
            = strpos($url, '?') === false ? $url . '?service=css' : $url;
        }

        return new CSSInliningHTMLFilter(
            URL::fromString($config['documents']['baseUrl']),
            $config['documents']['filters'][CSSInliningHTMLFilter::class],
            $retriever
        );
    }

}
