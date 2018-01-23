<?php

namespace Kibo\Phast\Services\Css;

use Kibo\Phast\Common\CSSURLRewriter;
use Kibo\Phast\Filters\HTML\ImagesOptimizationService\ImageURLRewriter;
use Kibo\Phast\Retrievers\Retriever;
use Kibo\Phast\Security\ServiceSignature;
use Kibo\Phast\Services\ProxyBaseService;
use Kibo\Phast\ValueObjects\URL;

class Service extends ProxyBaseService {

    /**
     * @var ImageURLRewriter
     */
    private $imageUrlRewriter;

    public function __construct(ServiceSignature $signature, Retriever $retriever, ImageURLRewriter $imageURLRewriter) {
        parent::__construct($signature, [], $retriever);
        $this->imageUrlRewriter = $imageURLRewriter;
    }

    protected function handle(array $request) {
        $response = parent::handle($request);
        $response->setHeader('Content-Type', 'text/css');
        return $response;
    }


    protected function doRequest(array $request) {
        $content = parent::doRequest($request);
        $base = URL::fromString($request['src']);
        $content = (new CSSURLRewriter())->rewriteRelativeURLs($content, $base);
        return $this->imageUrlRewriter->rewriteStyle($content);
    }
}
