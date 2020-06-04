<?php
namespace Kibo\Phast\Filters\Text\Decode;

use Kibo\Phast\Services\ServiceFilter;
use Kibo\Phast\ValueObjects\Resource;

class Filter implements ServiceFilter {
    const UTF8_BOM = "\xef\xbb\xbf";

    public function apply(Resource $resource, array $request = []) {
        $content = $resource->getContent();
        if (substr($content, 0, strlen(self::UTF8_BOM)) == self::UTF8_BOM) {
            $content = substr($content, strlen(self::UTF8_BOM));
        }
        return $resource->withContent($content);
    }
}
