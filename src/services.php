<?php

require_once __DIR__ . '/bootstrap.php';
\Kibo\Phast\PhastServices::serve(function () {
    return require_once PHAST_CONFIG_FILE;
});
