<?php

namespace Kibo\Phast\Services\Css;

use Kibo\Phast\Common\CSSURLRewriter;
use Kibo\Phast\Retrievers\Retriever;
use Kibo\Phast\Security\ServiceSignature;
use Kibo\Phast\Services\ProxyBaseService;
use Kibo\Phast\ValueObjects\URL;

class Service extends ProxyBaseService {

    public function __construct(ServiceSignature $signature, Retriever $retriever) {
        parent::__construct($signature, [], $retriever);
    }

    protected function handle(array $request) {
        $response = parent::handle($request);
        $response->setHeader('Content-Type', 'text/css');
        return $response;
    }


    protected function doRequest(array $request) {
        $content = parent::doRequest($request);
        $base = URL::fromString($request['src']);
        return (new CSSURLRewriter())->rewriteRelativeURLs($content, $base);
    }
}
