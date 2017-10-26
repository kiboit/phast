<?php
require_once __DIR__ . '/../vendor/autoload.php';
$config = require_once __DIR__ . '/config-example.php';
\Kibo\Phast\PhastDocumentFilters::deploy($config);
