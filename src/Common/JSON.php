<?php

namespace Kibo\Phast\Common;

class JSON {
    public static function encode($value) {
        return json_encode(
            $value,
            JSON_UNESCAPED_SLASHES |
            JSON_PARTIAL_OUTPUT_ON_ERROR
        );
    }

    public static function prettyEncode($value) {
        return json_encode(
            $value,
            JSON_UNESCAPED_SLASHES |
            JSON_PARTIAL_OUTPUT_ON_ERROR |
            JSON_PRETTY_PRINT
        );
    }
}
