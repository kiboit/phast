<?php
require_once __DIR__ . '/bootstrap.php';
$config = require_once PHAST_CONFIG_FILE;
try {
    list ($type, $image) = \Kibo\Phast\Factories\ImageFilteringServiceFactory::make($config)->serve($_GET);
    header('Content-Type: ' . $type);
    header('Cache-Control: max-age=' . (86400 * 365));
    echo $image;
} catch (\Kibo\Phast\Exceptions\ItemNotFoundException $e) {
    $url = $e->getUrl();
    if ($url) {
        header('Location: ' . $url);
    } else {
        http_response_code(404);
    }
} catch (\Kibo\Phast\Exceptions\UnauthorizedException $e) {
    http_response_code(403);
} catch (Exception $e) {
    http_response_code(500);
}
