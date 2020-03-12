<?php


namespace Kibo\Phast\Filters\CSS\ImportsStripper;

use Kibo\Phast\Filters\HTML\CSSInlining;
use Kibo\Phast\Filters\Service\CachedResultServiceFilter;
use Kibo\Phast\Logging\LoggingTrait;
use Kibo\Phast\ValueObjects\Resource;

class Filter implements CachedResultServiceFilter {
    use LoggingTrait;

    public function getCacheSalt(Resource $resource, array $request) {
        return $this->shouldStripImports($request) ? 'strip-imports' : 'no-strip-imports';
    }

    public function apply(Resource $resource, array $request) {
        if (!$this->shouldStripImports($request)) {
            $this->logger()->info('No import stripping requested! Skipping!');
            return $resource;
        }
        $css = $resource->getContent();
        $stripped = preg_replace(CSSInlining\Filter::CSS_IMPORTS_REGEXP, '', $css);
        return $resource->withContent($stripped);
    }

    private function shouldStripImports(array $request) {
        return isset($request['strip-imports']);
    }
}
