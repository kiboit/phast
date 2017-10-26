<?php
require_once __DIR__ . '/bootstrap.php';
$config = require_once PHAST_CONFIG_FILE;
try {
    list ($type, $image) = \Kibo\Phast\Factories\ImageFilteringServiceFactory::make($config)->serve($_GET);
    if ($type == \Kibo\Phast\Filters\Image\Image::TYPE_JPEG) {
        header('Content-type: image/jpeg');
    } else if ($type == \Kibo\Phast\Filters\Image\Image::TYPE_PNG) {
        header('Content-type: image/png');
    }
    echo $image;
} catch (\Kibo\Phast\Exceptions\ItemNotFoundException $exception) {
    http_response_code(404);
} catch (\Kibo\Phast\Exceptions\UnauthorizedException $e) {
    http_response_code(403);
} catch (Exception $e) {
    http_response_code(500);
}
