<?php

namespace Kibo\Phast\Services;

use Kibo\Phast\Retrievers\Retriever;
use Kibo\Phast\Security\ServiceSignature;

class CSSProxyService extends ProxyService {

    public function __construct(ServiceSignature $signature, array $whitelist, Retriever $retriever) {
        $whiteListParsed = [];
        foreach ($whitelist as $key => $value) {
            if (is_array($value)) {
                $whiteListParsed[] = $key;
            } else {
                $whiteListParsed[] = $value;
            }
        }
        parent::__construct($signature, $whiteListParsed, $retriever);
    }

    protected function handle(array $request) {
        $response = parent::handle($request);
        $response->setHeader('Content-Type', 'text/css');
        return $response;
    }

}
