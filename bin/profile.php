<?php

if (PHP_SAPI != 'cli') {
    die ('CLI sript');
}

if (!isset ($argv[1])) {
    die ("Missing \$argv[1]. Specify a php script which must return a callable to profile.\n");
}

if (!isset ($argv[2])) {
    die ("Missing \$argv[2]. Specify a number of iterations.");
}

require_once __DIR__ . '/../vendor/autoload.php';

$file = $argv[1];
$iterations = (int)$argv[2];

$callback = require_once $file;
if (!is_callable($callback)) {
    die ("$file did not return a callable");
}

tideways_xhprof_enable();

for ($i = 0; $i < $iterations; $i++) {
    $callback();
}

echo serialize(tideways_xhprof_disable());
