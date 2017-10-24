<?php

namespace Kibo\Phast\Factories;

use Kibo\Phast\Filters\ImagesOptimizationServiceHTMLFilter;
use Kibo\Phast\Security\ImagesOptimizationSignature;
use Kibo\Phast\ValueObjects\URL;

class ImagesOptimizationServiceHTMLFilterFactory implements HTMLFilterFactory {

    public function make(array $config) {
        $signature = new ImagesOptimizationSignature($config['securityToken']);
        return new ImagesOptimizationServiceHTMLFilter(
            $signature,
            URL::fromString($config['referrerUrl']),
            URL::fromString($config['serviceUrl'])
        );
    }

}
