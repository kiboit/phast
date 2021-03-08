<?php


namespace Kibo\Phast\Filters\CSS\CSSURLRewriter;

use Kibo\Phast\Services\ServiceFilter;
use Kibo\Phast\ValueObjects\Resource;
use Kibo\Phast\ValueObjects\URL;

class Filter implements ServiceFilter {
    /**
     * @param Resource $resource
     * @param array $request
     * @return Resource
     */
    public function apply(Resource $resource, array $request) {
        $baseUrl = $resource->getUrl();
        $callback = function ($match) use ($baseUrl) {
            if (preg_match('~^[a-z]+:|^#~i', $match[3])) {
                return $match[0];
            }
            return $match[1] . URL::fromString($match[3])->withBase($baseUrl) . $match[4];
        };

        $cssContent = preg_replace_callback(
            '~
                \b
                ( url\( \s*+ ([\'"]?) )
                ([A-Za-z0-9_/.:?&=+%,#@-]+)
                ( \2 \s*+ \) )
            ~x',
            $callback,
            $resource->getContent()
        );

        $cssContent = preg_replace_callback(
            '~
                ( @import \s+ ([\'"]) )
                ([A-Za-z0-9_/.:?&=+%,#@-]+)
                ( \2 )
            ~x',
            $callback,
            $cssContent
        );

        return $resource->withContent($cssContent);
    }
}
