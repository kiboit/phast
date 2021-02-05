<?php


namespace Kibo\Phast\Filters\JavaScript\Minification;

use Kibo\Phast\Common\JSMinifier;
use Kibo\Phast\Filters\Service\CachedResultServiceFilter;
use Kibo\Phast\ValueObjects\Resource;

class JSMinifierFilter implements CachedResultServiceFilter {
    const VERSION = 3;

    private $removeLicenseHeaders = true;

    /**
     * JSMinifierFilter constructor.
     * @param bool $removeLicenseHeaders
     */
    public function __construct($removeLicenseHeaders) {
        $this->removeLicenseHeaders = (bool) $removeLicenseHeaders;
    }

    public function getCacheSalt(Resource $resource, array $request) {
        return http_build_query([
            'v' => self::VERSION,
            'removeLicenseHeaders' => $this->removeLicenseHeaders,
        ]);
    }

    public function apply(Resource $resource, array $request) {
        $minified = (new JSMinifier($resource->getContent(), $this->removeLicenseHeaders))->min();
        return $resource->withContent($minified);
    }
}
