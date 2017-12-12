<?php

namespace Kibo\Phast\Factories\Filters\HTML;

use Kibo\Phast\Factories\Security\ServiceSignatureFactory;
use Kibo\Phast\Filters\HTML\ImagesOptimizationServiceHTMLFilter;
use Kibo\Phast\Retrievers\LocalRetriever;
use Kibo\Phast\ValueObjects\URL;

class ImagesOptimizationServiceHTMLFilterFactory implements HTMLFilterFactory {

    protected $class = ImagesOptimizationServiceHTMLFilter::class;

    public function make(array $config) {
        $signature = (new ServiceSignatureFactory())->make($config);
        if (isset ($config['documents']['filters'][$this->class]['serviceUrl'])) {
            $serviceUrl = $config['documents']['filters'][$this->class]['serviceUrl'];
        } else {
            $serviceUrl = $config['servicesUrl'] . '?service=images';
        }
        $class = $this->class;
        return new $class(
            $signature,
            new LocalRetriever($config['retrieverMap']),
            URL::fromString($config['documents']['baseUrl']),
            URL::fromString($serviceUrl),
            $config['images']['whitelist'],
            @$config['documents']['filters'][$this->class]['rewriteFormat']
        );
    }

}
