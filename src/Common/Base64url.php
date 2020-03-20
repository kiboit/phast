<?php
namespace Kibo\Phast\Common;

class Base64url {
    public static function encode($data) {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    public static function decode($data) {
        return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
    }

    public static function shortHash($data) {
        return self::encode(substr(sha1($data, true), 0, 8));
    }
}
