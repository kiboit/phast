<?php

namespace Kibo\Phast\Factories\Filters\HTML;

use Kibo\Phast\Cache\FileCache;
use Kibo\Phast\Filters\HTML\CSSInliningHTMLFilter;
use Kibo\Phast\Retrievers\LocalRetriever;
use Kibo\Phast\Retrievers\ProxyServiceCacheRetriever;
use Kibo\Phast\Retrievers\UniversalRetriever;
use Kibo\Phast\ValueObjects\URL;

class CSSInliningHTMLFilterFactory implements HTMLFilterFactory {

    public function make(array $config) {
        $retriever = new UniversalRetriever();
        $retriever->addRetriever(new LocalRetriever($config['retrieverMap']));
        $retriever->addRetriever(
            new ProxyServiceCacheRetriever(
                new FileCache($config['cache'], 'css'),
                $config['documents']['filters'][CSSInliningHTMLFilter::class]['urlRefreshTime']
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
