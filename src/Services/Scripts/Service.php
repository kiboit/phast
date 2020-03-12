<?php

namespace Kibo\Phast\Services\Scripts;

use Kibo\Phast\Services\ProxyBaseService;
use Kibo\Phast\ValueObjects\Resource;

class Service extends ProxyBaseService {
    protected function makeResponse(Resource $resource, array $request) {
        $response = parent::makeResponse($resource, $request);
        $response->setHeader('Content-Type', 'application/javascript');
        return $response;
    }
}
