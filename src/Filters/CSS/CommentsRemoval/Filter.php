<?php


namespace Kibo\Phast\Filters\CSS\CommentsRemoval;

use Kibo\Phast\Services\ServiceFilter;
use Kibo\Phast\ValueObjects\Resource;

class Filter implements ServiceFilter {
    public function apply(Resource $resource, array $request) {
        $content = preg_replace('~/\*[^*]*\*+([^/*][^*]*\*+)*/~', '', $resource->getContent());
        return $resource->withContent($content);
    }
}
