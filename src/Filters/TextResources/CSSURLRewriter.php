<?php


namespace Kibo\Phast\Filters\TextResources;

use Kibo\Phast\ValueObjects\URL;

class CSSURLRewriter implements TextResourceFilter {


    public function transform(TextResource $resource) {
        $baseUrl = $resource->getLocation();
        $callback = function($match) use ($baseUrl) {
            if (preg_match('~^[a-z]+:~i', $match[3])) {
                return $match[0];
            }
            return $match[1] . URL::fromString($match[3])->withBase($baseUrl) . $match[4];
        };

        $cssContent = preg_replace_callback(
            '~
                \b
                ( url\( ([\'"]?) )
                ([A-Za-z0-9_/.:?&=+%,#-]+)
                ( \2 \) )
            ~x',
            $callback,
            $resource->getContent()
        );

        $cssContent = preg_replace_callback(
            '~
                ( @import \s+ ([\'"]) )
                ([A-Za-z0-9_/.:?&=+%,#-]+)
                ( \2 )
            ~x',
            $callback,
            $cssContent
        );

        return $resource->modifyContent($cssContent);
    }

}
