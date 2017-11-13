<?php

namespace Kibo\Phast\Services;

use JSMin\JSMin;

class ScriptsProxyService extends ProxyService {

    protected function handle(array $request) {
        $response = parent::handle($request);
        $response->setHeader('Content-Type', 'application/javascript');
        return $response;
    }

    protected function doRequest(array $request) {
        $result = parent::doRequest($request);
        return JSMin::minify($result);
    }

}
