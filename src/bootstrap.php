<?php
spl_autoload_register(function ($class) {
    if (!preg_match('/^Kibo\\\\Phast/', $class)) {
        return;
    }
    $path = str_replace('\\', '/', str_replace('Kibo\Phast', '', $class)) . '.php';
    require_once __DIR__ . $path;
});

if (!defined('PHAST_CONFIG_FILE')) {
    define('PHAST_CONFIG_FILE', __DIR__ . '/config-default.php');
}
