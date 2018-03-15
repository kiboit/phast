<?php


namespace Kibo\Phast\Filters\JavaScript\Composite;


use Kibo\Phast\Filters\Service\CachedResultServiceFilter;
use Kibo\Phast\Filters\Service\CompositeFilter;
use Kibo\Phast\ValueObjects\Resource;

class Filter extends CompositeFilter implements CachedResultServiceFilter {

    private $removeLicenseHeaders = true;

    /**
     * JSMinifierFilter constructor.
     * @param bool $removeLicenseHeaders
     */
    public function __construct($removeLicenseHeaders) {
        $this->removeLicenseHeaders = (bool) $removeLicenseHeaders;
    }

    public function getCacheHash(Resource $resource, array $request) {
        return md5($resource->getContent()) . $this->removeLicenseHeaders;
    }

}
