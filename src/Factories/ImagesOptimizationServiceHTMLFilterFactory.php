<?php

namespace Kibo\Phast\Factories;

use Kibo\Phast\Filters\ImagesOptimizationServiceHTMLFilter;
use Kibo\Phast\Security\ImagesOptimizationSignature;

class ImagesOptimizationServiceHTMLFilterFactory implements HTMLFilterFactory {

    public function make(array $config) {
        $signature = new ImagesOptimizationSignature($config['securityToken']);
        return new ImagesOptimizationServiceHTMLFilter(
            $signature,
            $config['referrerUrl'],
            $config['serviceUrl']
        );
    }

}
