<?php


namespace Kibo\Phast\Filters\CSS\ImportsStripper;


use Kibo\Phast\Filters\HTML\CSSInlining;
use Kibo\Phast\Logging\LoggingTrait;
use Kibo\Phast\Services\ServiceFilter;
use Kibo\Phast\ValueObjects\Resource;

class Filter implements ServiceFilter {
    use LoggingTrait;

    public function apply(Resource $resource, array $request) {
        if (!isset ($request['strip-imports'])) {
            $this->logger()->info('No import stripping requested! Skipping!');
            return $resource;
        }
        $css = $resource->getContent();
        $stripped = preg_replace(CSSInlining\Filter::CSS_IMPORTS_REGEXP, '', $css);
        return $resource->withContent($stripped);
    }

}
