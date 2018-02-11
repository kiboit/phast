<?php
if (!class_exists('\Kibo\Phast\PhastServices')) {
    if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
        require_once __DIR__ . '/../vendor/autoload.php';
    } elseif (file_exists(__DIR__ . '/../../../autoload.php')) {
        require_once __DIR__ . '/../../../autoload.php';
    }
}
if (!defined('PHAST_CONFIG_FILE')) {
    define('PHAST_CONFIG_FILE', __DIR__ . '/config-user.php');
}
