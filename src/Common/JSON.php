<?php

namespace Kibo\Phast\Common;

class JSON {
    public static function encode($value) {
        return self::_encode($value, 0);
    }

    public static function prettyEncode($value) {
        return self::_encode($value, JSON_PRETTY_PRINT);
    }

    private static function _encode($value, $flags) {
        $flags |= JSON_UNESCAPED_SLASHES;

        if (version_compare(PHP_VERSION, '7.2.0', '<')) {
            return self::legacyEncode($value, $flags);
        }

        return json_encode(
            $value,
            $flags | JSON_INVALID_UTF8_IGNORE | JSON_PARTIAL_OUTPUT_ON_ERROR
        );
    }

    private static function legacyEncode($value, $flags) {
        $result = json_encode($value, $flags);
        if ($result !== false || json_last_error() !== JSON_ERROR_UTF8) {
            return $result;
        }

        self::cleanUTF8($value);
        return json_encode($value, $flags | JSON_PARTIAL_OUTPUT_ON_ERROR);
    }

    private static function cleanUTF8(&$value) {
        if (is_array($value)) {
            array_walk_recursive($value, __METHOD__);
        } elseif (is_string($value)) {
            $value = preg_replace_callback(
                '~
                    [\x00-\x7F]++                      # ASCII
                  | [\xC2-\xDF][\x80-\xBF]             # non-overlong 2-byte
                  |  \xE0[\xA0-\xBF][\x80-\xBF]        # excluding overlongs
                  | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}  # straight 3-byte
                  |  \xED[\x80-\x9F][\x80-\xBF]        # excluding surrogates
                  |  \xF0[\x90-\xBF][\x80-\xBF]{2}     # planes 1-3
                  | [\xF1-\xF3][\x80-\xBF]{3}          # planes 4-15
                  |  \xF4[\x80-\x8F][\x80-\xBF]{2}     # plane 16
                  | (.)
                ~xs',
                function ($match) {
                    if (isset($match[1]) && strlen($match[1])) {
                        return '';
                    }
                    return $match[0];
                },
                $value
            );
        }
    }
}
