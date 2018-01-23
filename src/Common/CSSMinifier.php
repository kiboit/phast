<?php

namespace Kibo\Phast\Common;


class CSSMinifier {

    /**
     * @param string $content
     * @return string
     */
    public function minify($content) {
        // Remove comments
        $content = preg_replace('~/\*[^*]*\*+([^/*][^*]*\*+)*/~', '', $content);

        // Normalize whitespace
        $content = preg_replace('~\s+~', ' ', $content);

        // Remove whitespace before and after operators
        $chars = [',', '{', '}', ';'];
        foreach ($chars as $char) {
            $content = str_replace("$char ", $char, $content);
            $content = str_replace(" $char", $char, $content);
        }

        // Remove whitespace after colons
        $content = str_replace(': ', ':', $content);

        return trim($content);
    }

}
