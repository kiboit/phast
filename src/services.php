<?php

require_once __DIR__ . '/bootstrap.php';
$serviceRequest = \Kibo\Phast\Services\ServiceRequest::fromHTTPRequest(
    \Kibo\Phast\HTTP\Request::fromGlobals()
);
$serviceParams = $serviceRequest->getParams();

if (defined('PHAST_SERVICE')) {
    $service = PHAST_SERVICE;
} else if (!isset ($serviceParams['service'])) {
    http_response_code(404);
    exit;
} else {
    $service = $serviceParams['service'];
}

if (isset ($serviceParams['src']) && !headers_sent())  {
    header('Location: ' . $serviceParams['src']);
} else {
    http_response_code(404);
    exit;
}

$config = require_once PHAST_CONFIG_FILE;
try {
    $runtimeConfig = (new \Kibo\Phast\Environment\Configuration($config))
        ->withServiceRequest($serviceRequest)
        ->toArray();
    $response = (new \Kibo\Phast\Factories\Services\ServicesFactory())
        ->make($service, $runtimeConfig)
        ->serve($serviceRequest);
} catch (\Kibo\Phast\Exceptions\UnauthorizedException $e) {
    exit();
}

header_remove('Location');
http_response_code($response->getCode());
foreach ($response->getHeaders() as $name => $value) {
    header($name . ': ' . $value);
}

echo $response->getContent();
