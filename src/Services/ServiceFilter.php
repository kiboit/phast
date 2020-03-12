<?php


namespace Kibo\Phast\Services;

use Kibo\Phast\ValueObjects\Resource;

interface ServiceFilter {
    /**
     * @param Resource $resource
     * @param array $request
     * @return Resource
     */
    public function apply(Resource $resource, array $request);
}
