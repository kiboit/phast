<?php

namespace Kibo\Phast\Services\Scripts;

use Kibo\Phast\Services\ProxyBaseService;
use Kibo\Phast\ValueObjects\Resource;

class Service extends ProxyBaseService {

    private $cleanupFunction = <<<EOF
(function (param) {
    var script = document.querySelector('[data-phast-proxied-script="' + param.i + '"]');
    if (script) {
        script.removeAttribute('data-phast-proxied-script');
        script.setAttribute('src', param.s);
    }
})
EOF;

    protected function makeResponse(Resource $resource, array $request) {
        $resource = $this->addCleanupScript($resource, $request);
        $response = parent::makeResponse($resource, $request);
        $response->setHeader('Content-Type', 'application/javascript');
        return $response;
    }

    private function addCleanupScript(Resource $resource, $request) {
        if (empty($request['src'])
            || empty($request['id'])
            || !preg_match('/^(?:[a-z0-9]+-)*[a-z0-9]+$/', $request['id'])
        ) {
            return $resource;
        }

        $param = [
            'i' => $request['id'],
            's' => $request['src']
        ];
        $script = $this->cleanupFunction . '(' . json_encode($param) . ');';

        return $resource->withContent($script . $resource->getContent());
    }

}
