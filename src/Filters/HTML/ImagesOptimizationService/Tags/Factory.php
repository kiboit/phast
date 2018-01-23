<?php

namespace Kibo\Phast\Filters\HTML\ImagesOptimizationService\Tags;

use Kibo\Phast\Filters\HTML\HTMLFilterFactory;
use Kibo\Phast\Filters\HTML\ImagesOptimizationService\ImageURLRewriter;
use Kibo\Phast\Retrievers\LocalRetriever;
use Kibo\Phast\Security\ServiceSignatureFactory;
use Kibo\Phast\ValueObjects\URL;

class Factory implements HTMLFilterFactory {

    protected $class = Filter::class;

    public function make(array $config) {
        $signature = (new ServiceSignatureFactory())->make($config);
        if (isset ($config['documents']['filters'][$this->class]['serviceUrl'])) {
            $serviceUrl = $config['documents']['filters'][$this->class]['serviceUrl'];
        } else {
            $serviceUrl = $config['servicesUrl'] . '?service=images';
        }
        $rewriter = new ImageURLRewriter(
            $signature,
            new LocalRetriever($config['retrieverMap']),
            URL::fromString($config['documents']['baseUrl']),
            URL::fromString($serviceUrl),
            $config['images']['whitelist'],
            @$config['documents']['filters'][$this->class]['rewriteFormat']
        );
        $class = $this->class;
        return new $class($rewriter);
    }

}
