<?php

namespace Kibo\Phast\Services\Css;

use Kibo\Phast\Filters\HTML\ImagesOptimizationService\ImageURLRewriter;
use Kibo\Phast\Filters\TextResources\TextResource;
use Kibo\Phast\Filters\TextResources\TextResourceFilter;
use Kibo\Phast\Retrievers\Retriever;
use Kibo\Phast\Security\ServiceSignature;
use Kibo\Phast\Services\ProxyBaseService;
use Kibo\Phast\ValueObjects\URL;

class Service extends ProxyBaseService {

    /**
     * @var TextResourceFilter
     */
    private $filter;

    /**
     * @var ImageURLRewriter
     */
    private $imageUrlRewriter;

    public function __construct(ServiceSignature $signature, Retriever $retriever, ImageURLRewriter $imageURLRewriter, TextResourceFilter $filter) {
        parent::__construct($signature, [], $retriever);
        $this->imageUrlRewriter = $imageURLRewriter;
        $this->filter = $filter;
    }

    protected function handle(array $request) {
        $response = parent::handle($request);
        $response->setHeader('Content-Type', 'text/css');
        return $response;
    }


    protected function doRequest(array $request) {
        $content = parent::doRequest($request);
        $resource = new TextResource(URL::fromString($request['src']), $content);
        $resource = $this->filter->transform($resource);
        return $this->imageUrlRewriter->rewriteStyle($resource->getContent());
    }
}
