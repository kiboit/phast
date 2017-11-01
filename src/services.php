<?php
if (!isset ($_GET['service'])) {
    http_response_code(404);
    exit;
}
$service = $_GET['service'];
if (!in_array($service, ['scripts', 'images'])) {
    http_response_code(404);
    exit;
}
require __DIR__ . '/' . $service . '.php';
