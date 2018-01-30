<?php

// TODO: Capitalize the css ns
namespace Kibo\Phast\Services\Css;

use Kibo\Phast\Filters\HTML\ImagesOptimizationService\ImageURLRewriter;
use Kibo\Phast\Retrievers\Retriever;
use Kibo\Phast\Security\ServiceSignature;
use Kibo\Phast\Services\ProxyBaseService;
use Kibo\Phast\Services\ServiceFilter;
use Kibo\Phast\ValueObjects\Resource;

class Service extends ProxyBaseService {

    /**
     * @var ImageURLRewriter
     */
    private $imageUrlRewriter;

    public function __construct(
        ServiceSignature $signature,
        $whitelist,
        Retriever $retriever,
        ServiceFilter $filter,
        ImageURLRewriter $imageURLRewriter
    ) {
        parent::__construct($signature, $whitelist, $retriever, $filter);
        $this->imageUrlRewriter = $imageURLRewriter;
    }


    protected function makeResponse(Resource $resource, array $request) {
        $response = parent::makeResponse($resource, $request);
        $response->setHeader('Content-Type', 'text/css');
        $response->setContent($this->imageUrlRewriter->rewriteStyle($response->getContent()));
        return $response;
    }
}
