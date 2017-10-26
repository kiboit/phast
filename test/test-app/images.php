<?php
require_once __DIR__ . '/../../vendor/autoload.php';
$config = require_once __DIR__ . '/../../src/config-example.php';
\Kibo\Phast\ImageFilteringService::serve($config['images'], $_GET);
