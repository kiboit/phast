<?php

if (PHP_SAPI != 'cli') {
    echo "This script must be run from the command line.\n";
    exit(1);
}

if (sizeof($argv) != 3) {
    echo "Run a script that returns in callable inside a profiling loop for\n";
    echo "the specified number of iterations.\n\n";
    echo "Usage: {$argv[0]} <script> <iterations>\n";
    exit(1);
}

require_once __DIR__ . '/../vendor/autoload.php';

$file = $argv[1];
$iterations = (int)$argv[2];

$callback = require_once $file;
if (!is_callable($callback)) {
    echo "$file did not return a callable\n";
    exit(1);
}

tideways_xhprof_enable();

for ($i = 0; $i < $iterations; $i++) {
    $callback();
}

echo serialize(tideways_xhprof_disable());
