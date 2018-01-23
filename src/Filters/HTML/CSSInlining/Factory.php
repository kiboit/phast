<?php

namespace Kibo\Phast\Filters\HTML\CSSInlining;

use Kibo\Phast\Cache\File\Cache;
use Kibo\Phast\Common\CSSMinifier;
use Kibo\Phast\Filters\HTML\HTMLFilterFactory;
use Kibo\Phast\Retrievers\CachingRetriever;
use Kibo\Phast\Retrievers\LocalRetriever;
use Kibo\Phast\Retrievers\UniversalRetriever;
use Kibo\Phast\Security\ServiceSignatureFactory;
use Kibo\Phast\ValueObjects\URL;

class Factory implements HTMLFilterFactory {

    public function make(array $config) {
        $retriever = new UniversalRetriever();
        $retriever->addRetriever(new LocalRetriever($config['retrieverMap']));
        $retriever->addRetriever(
            new CachingRetriever(
                new Cache($config['cache'], 'css')
            )
        );

        if (!isset ($config['documents']['filters'][Filter::class]['serviceUrl'])) {
            $url = $config['servicesUrl'];
            $config['documents']['filters'][Filter::class]['serviceUrl']
            = strpos($url, '?') === false ? $url . '?service=css' : $url;
        }

        return new Filter(
            (new ServiceSignatureFactory())->make($config),
            URL::fromString($config['documents']['baseUrl']),
            $config['documents']['filters'][Filter::class],
            $retriever,
            new OptimizerFactory(),
            new CSSMinifier()
        );
    }

}
