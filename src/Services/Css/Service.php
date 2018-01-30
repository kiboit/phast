<?php

namespace Kibo\Phast\Services\Css;

use Kibo\Phast\Filters\HTML\ImagesOptimizationService\ImageURLRewriter;
use Kibo\Phast\Retrievers\Retriever;
use Kibo\Phast\Security\ServiceSignature;
use Kibo\Phast\Services\ProxyBaseService;
use Kibo\Phast\Services\ServiceFilter;
use Kibo\Phast\ValueObjects\Resource;

class Service extends ProxyBaseService {

    protected function makeResponse(Resource $resource, array $request) {
        $response = parent::makeResponse($resource, $request);
        $response->setHeader('Content-Type', 'text/css');
        return $response;
    }
}
