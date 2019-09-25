<?php
require __DIR__ . '/../../build/phast.php';
\Kibo\Phast\PhastServices::serve(function () {
    return require __DIR__ . '/test-config.php';
});
