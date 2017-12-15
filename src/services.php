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
}

$config = require_once PHAST_CONFIG_FILE;
try {
    $runtimeConfig = (new \Kibo\Phast\Environment\Configuration($config))
        ->withServiceRequest($serviceRequest)
        ->toArray();
    \Kibo\Phast\Logging\Log::init($runtimeConfig['logging'], $serviceRequest, $service);
    \Kibo\Phast\Logging\Log::info('Starting service');
    $response = (new \Kibo\Phast\Services\Factory())
        ->make($service, $runtimeConfig)
        ->serve($serviceRequest);
    \Kibo\Phast\Logging\Log::info('Service completed!');
} catch (\Kibo\Phast\Exceptions\UnauthorizedException $e) {
    \Kibo\Phast\Logging\Log::error('Unauthorized exception: {message}!', ['message' => $e->getMessage()]);
    exit();
} catch (\Kibo\Phast\Exceptions\ItemNotFoundException $e) {
    \Kibo\Phast\Logging\Log::error('Item not found: {message}', ['message' => $e->getMessage()]);
    exit();
} catch (Exception $e) {
    \Kibo\Phast\Logging\Log::critical(
        'Unhandled exception: {type} Message: {message} File: {file} Line: {line}',
        ['type' => get_class($e), 'message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]
    );
    exit();
}

header_remove('Location');
http_response_code($response->getCode());
foreach ($response->getHeaders() as $name => $value) {
    header($name . ': ' . $value);
}

echo $response->getContent();
