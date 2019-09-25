<?php
require __DIR__ . '/../../build/phast.php';
\Kibo\Phast\PhastServices::serve(function () {
    return require __DIR__ . '/phast-config.php';
});
