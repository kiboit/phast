<?php

namespace Kibo\Phast\Factories\Filters\HTML;

use Kibo\Phast\Factories\Security\ServiceSignatureFactory;
use Kibo\Phast\Filters\HTML\ImagesOptimizationServiceHTMLFilter;
use Kibo\Phast\ValueObjects\URL;

class ImagesOptimizationServiceHTMLFilterFactory implements HTMLFilterFactory {

    public function make(array $config) {
        $signature = (new ServiceSignatureFactory())->make($config);
        return new ImagesOptimizationServiceHTMLFilter(
            $signature,
            URL::fromString($config['documents']['baseUrl']),
            URL::fromString(
                $config['documents']['filters'][ImagesOptimizationServiceHTMLFilter::class]['serviceUrl']
            )
        );
    }

}
