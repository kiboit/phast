<?php

spl_autoload_register(function ($class) {
    $namespaces = [
        'Kibo\\Phast' => 'test/php',
    ];
    $parts = explode('\\', $class);
    foreach ($namespaces as $ns => $dir) {
        $nsParts = explode('\\', $ns);
        if ($nsParts != array_slice($parts, 0, sizeof($nsParts))) {
            continue;
        }
        $path = $dir . '/' . implode('/', array_slice($parts, sizeof($nsParts))) . '.php';
        if (file_exists($path)) {
            require $path;
        }
    }
});

spl_autoload_register(function ($class) {
    $namespaces = [
        'PHPUnit' => 'vendor/phpunit/phpunit/src',
    ];
    $parts = explode('_', $class);
    foreach ($namespaces as $ns => $dir) {
        $nsParts = explode('_', $ns);
        if ($nsParts != array_slice($parts, 0, sizeof($nsParts))) {
            continue;
        }
        $path = $dir . '/' . implode('/', array_slice($parts, sizeof($nsParts))) . '.php';
        if (file_exists($path)) {
            require $path;
        }
    }
});

require_once __DIR__ . '/../../build/phast.php';
