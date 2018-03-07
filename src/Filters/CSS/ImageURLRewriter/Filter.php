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
        $content = $this->rewriter->rewriteStyle($resource->getContent());
        $dependencies = $this->rewriter->getInlinedResources();
        return $resource->withContent($content)
                        ->withDependencies($dependencies);
    }

}
