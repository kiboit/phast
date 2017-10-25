<?php

namespace Kibo\Phast\Factories\Filters\HTML;

use Kibo\Phast\Filters\HTML\ImagesOptimizationServiceHTMLFilter;
use Kibo\Phast\Security\ImagesOptimizationSignature;
use Kibo\Phast\ValueObjects\URL;

class ImagesOptimizationServiceHTMLFilterFactory implements HTMLFilterFactory {

    public function make(array $config) {
        $signature = new ImagesOptimizationSignature($config['securityToken']);
        return new ImagesOptimizationServiceHTMLFilter(
            $signature,
            URL::fromString($config['baseUrl']),
            URL::fromString($config['serviceUrl'])
        );
    }

}
