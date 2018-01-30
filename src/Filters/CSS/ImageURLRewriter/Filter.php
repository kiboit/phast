<?php

namespace Kibo\Phast\Filters\CSS\ImageURLRewriter;

use Kibo\Phast\Filters\HTML\ImagesOptimizationService\ImageURLRewriter;
use Kibo\Phast\Services\ServiceFilter;
use Kibo\Phast\ValueObjects\Resource;

class Filter implements ServiceFilter {

    /**
     * @var ImageURLRewriter
     */
    private $rewriter;

    /**
     * Filter constructor.
     * @param ImageURLRewriter $rewriter
     */
    public function __construct(ImageURLRewriter $rewriter) {
        $this->rewriter = $rewriter;
    }


    public function apply(Resource $resource, array $request) {
        return $resource->withContent(
            $this->rewriter->rewriteStyle($resource->getContent())
        );
    }

}
