<?php


namespace Kibo\Phast\Filters\Service\Compression;


use Kibo\Phast\Services\ServiceFilter;
use Kibo\Phast\ValueObjects\Resource;

class DecompressingFilter implements ServiceFilter {

    public function apply(Resource $resource, array $request) {
        if (!$this->isCompressed($resource)  || $this->acceptsCompressed($request)) {
            return $resource;
        }
        return $resource->withContent(gzdecode($resource->getContent()));
    }

    private function isCompressed(Resource $resource) {
        return $resource->getEncoding() == 'gzip';
    }

    private function acceptsCompressed(array $request) {
        return count(array_intersect(['gzip', '*'], (array) @$request['accept-encoding'])) > 0;
    }


}
