<?php


namespace Kibo\Phast\Filters\Service;

use Kibo\Phast\Services\ServiceFilter;
use Kibo\Phast\ValueObjects\Resource;

interface CachedResultServiceFilter extends ServiceFilter {
    /**
     * @param Resource $resource
     * @param array $request
     * @return string
     */
    public function getCacheSalt(Resource $resource, array $request);
}
