<?php
if (!class_exists('\Kibo\Phast\PhastServices')) {
    require_once __DIR__ . '/../vendor/autoload.php';
}
if (!defined('PHAST_CONFIG_FILE')) {
    define('PHAST_CONFIG_FILE', __DIR__ . '/config-user.php');
}
