<?php


namespace Kibo\Phast\Filters\JavaScript\Minification;


use Kibo\Phast\Common\JSMinifier;
use Kibo\Phast\Filters\Service\CachedResultServiceFilter;
use Kibo\Phast\ValueObjects\Resource;

class JSMinifierFilter implements CachedResultServiceFilter {

    private $removeLicenseHeaders = true;

    /**
     * JSMinifierFilter constructor.
     * @param bool $removeLicenseHeaders
     */
    public function __construct($removeLicenseHeaders) {
        $this->removeLicenseHeaders = $removeLicenseHeaders;
    }

    public function getCacheHash(Resource $resource, array $request) {
        return md5($resource->getContent());
    }

    public function apply(Resource $resource, array $request) {
        $minified = (new JSMinifier($resource->getContent(), $this->removeLicenseHeaders))->min();
        return $resource->withContent($minified);
    }


}
