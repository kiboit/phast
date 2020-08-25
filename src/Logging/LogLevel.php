<?php
namespace Kibo\Phast\Logging;

class LogLevel {
    const EMERGENCY = 128;

    const ALERT     =  64;

    const CRITICAL  =  32;

    const ERROR     =  16;

    const WARNING   =   8;

    const NOTICE    =   4;

    const INFO      =   2;

    const DEBUG     =   1;

    public static function toString($level) {
        switch ($level) {
        case self::EMERGENCY:   return 'EMERGENCY';
        case self::ALERT:       return 'ALERT';
        case self::CRITICAL:    return 'CRITICAL';
        case self::ERROR:       return 'ERROR';
        case self::WARNING:     return 'WARNING';
        case self::NOTICE:      return 'NOTICE';
        case self::INFO:        return 'INFO';
        case self::DEBUG:       return 'DEBUG';
        default:                return 'UNKNOWN';
        }
    }
}
