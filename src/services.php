<?php
if (defined('PHAST_SERVICE')) {
    $service = PHAST_SERVICE;
} else if (!isset ($_GET['service'])) {
    http_response_code(404);
    exit;
} else {
    $service = $_GET['service'];
}

if (isset ($_GET['src']) && !headers_sent())  {
    header('Location: ' . $_GET['src']);
} else {
    http_response_code(404);
    exit;
}

require_once __DIR__ . '/bootstrap.php';
$config = require_once PHAST_CONFIG_FILE;
try {

    $response = (new \Kibo\Phast\Factories\Services\ServicesFactory())
        ->make($service, $config)
        ->serve(\Kibo\Phast\HTTP\Request::fromGlobals());
} catch (\Kibo\Phast\Exceptions\UnauthorizedException $e) {
    exit();
}

header_remove('Location');
http_response_code($response->getCode());
foreach ($response->getHeaders() as $name => $value) {
    header($name . ': ' . $value);
}

echo $response->getContent();
