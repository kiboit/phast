<?php
if (isset ($_GET['src']) && !headers_sent())  {
    header('Location: ' . $_GET['src']);
} else {
    http_response_code(404);
    exit;
}

require_once __DIR__ . '/bootstrap.php';
$config = require_once PHAST_CONFIG_FILE;
$service = new \Kibo\Phast\Services\ScriptsProxyService(
    new \Kibo\Phast\Security\ServiceSignature($config['securityToken'])
);
$output = $service->serve($_GET);

header_remove('Location');
http_response_code(200);
header('Content-Length: '  . strlen($output));
header('Cache-Control: max-age=' . (86400 * 365));
header('Content-Type: application/javascript');

echo $output;
