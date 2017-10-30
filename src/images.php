<?php
if (isset ($_GET['src']) && !headers_sent())  {
    header('Location: ' . $_GET['src']);
} else {
    http_response_code(404);
    exit;
}

require_once __DIR__ . '/bootstrap.php';
$config = require_once PHAST_CONFIG_FILE;
$image = \Kibo\Phast\Factories\ImageFilteringServiceFactory::make($config)->serve($_GET);
$type = $image->getType();
$output = $image->getAsString();

header_remove('Location');
http_response_code(200);
header('Content-Type: ' . $type);
header('Cache-Control: max-age=' . (86400 * 365));

echo $output;
