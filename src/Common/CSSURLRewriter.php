<?php


namespace Kibo\Phast\Common;

use Kibo\Phast\ValueObjects\URL;

class CSSURLRewriter {

    /**
     * @param string $cssContent
     * @param URL $baseUrl
     * @return string
     */
    public function rewriteRelativeURLs($cssContent, URL $baseUrl) {
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
            $cssContent
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

        return $cssContent;
    }

}
