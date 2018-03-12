<?php


namespace Kibo\Phast\Filters\Service\Compression;


use Kibo\Phast\Services\ServiceFilter;
use Kibo\Phast\ValueObjects\Resource;

class CompressingFilter implements ServiceFilter {

    public function apply(Resource $resource, array $request) {
        return $resource->withContent(
            gzencode($resource->getContent()),
            null,
            'gzip'
        );
    }

}
