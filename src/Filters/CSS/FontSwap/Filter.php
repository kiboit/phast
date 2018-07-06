<?php


namespace Kibo\Phast\Filters\CSS\FontSwap;


use Kibo\Phast\Services\ServiceFilter;
use Kibo\Phast\ValueObjects\Resource;

class Filter implements ServiceFilter {

    const FONT_FACE_REGEXP = '/@font-face\s*\{/i';

    public function apply(Resource $resource, array $request) {
        $css = $resource->getContent();
        $filtered = preg_replace(self::FONT_FACE_REGEXP, '$0font-display: swap;', $css);
        return $resource->withContent($filtered);
    }
}
