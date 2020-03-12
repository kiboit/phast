<?php
if (empty($_SERVER['HTTP_REFERER'])) {
    http_response_code(400);
    die('The request should be from the browser directly!');
}
header('Content-Type: text/css');
echo file_get_contents(__DIR__ . '/../res/stylesheet.css');
