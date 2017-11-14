<?php

namespace Kibo\Phast\Services;

use Kibo\Phast\Retrievers\Retriever;
use Kibo\Phast\Security\ServiceSignature;

class CSSProxyService extends ProxyService {

    public function __construct(ServiceSignature $signature, Retriever $retriever) {
        parent::__construct($signature, [], $retriever);
    }

    protected function handle(array $request) {
        $response = parent::handle($request);
        $response->setHeader('Content-Type', 'text/css');
        return $response;
    }

}
