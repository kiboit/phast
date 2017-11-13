<?php

namespace Kibo\Phast\Services;

class CSSProxyService extends ProxyService {

    protected function handle(array $request) {
        $response = parent::handle($request);
        $response->setHeader('Content-Type', 'text/css');
        return $response;
    }

}
