<?php
return call_user_func(function () {
    $config = require __DIR__ . '/../../src/config-default.php';
    $config['images']['enable-cache'] = true;
    $config['documents']['filters'][\Kibo\Phast\Filters\HTML\ScriptProxyServiceHTMLFilter::class]['match'][]
    = '~https?://ajax\.googleapis\.com/~';
    return $config;
});
