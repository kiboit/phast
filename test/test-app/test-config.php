<?php
return call_user_func(function () {
    $config = require __DIR__ . '/../../src/config-default.php';
    $config['documents']['filters'][\Kibo\Phast\Filters\HTML\ScriptsProxyService\Filter::class]['match'][]
    = '~https?://ajax\.googleapis\.com/~';
    return $config;
});
