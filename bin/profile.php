<?php

if (PHP_SAPI != 'cli') {
    fwrite(STDERR, "This script must be run from the command line.\n");
    exit(1);
}

if (sizeof($argv) != 3) {
    fwrite(STDERR, "Run a script that returns in callable inside a profiling loop for\n");
    fwrite(STDERR, "the specified number of iterations.\n\n");
    fwrite(STDERR, "Usage: {$argv[0]} <script> <iterations>\n");
    exit(1);
}

require_once __DIR__ . '/../vendor/autoload.php';

$file = $argv[1];
$iterations = (int) $argv[2];

$callback = require_once $file;
if (!is_callable($callback)) {
    fwrite(STDERR, "$file did not return a callable\n");
    exit(1);
}

$profile = function_exists('tideways_xhprof_enable');

if (!$profile) {
    fwrite(STDERR, "Warning: Tideways/XHProf extension is missing; not profiling...\n");
}

if ($profile) {
    tideways_xhprof_enable();
}

$timeStart = microtime(true);
for ($i = 0; $i < $iterations; $i++) {
    $callback();
}
$timeEnd = microtime(true);

$timeElapsed = $timeEnd - $timeStart;
fprintf(STDERR, "Ran %d iterations in %.4fs (%.4fs/iteration)\n", $iterations, $timeElapsed, $timeElapsed/$iterations);

if ($profile) {
    echo serialize(tideways_xhprof_disable());
}
