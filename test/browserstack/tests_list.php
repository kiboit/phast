<?php

return call_user_func(function () {
    $all_caps = json_decode(file_get_contents(__DIR__ . '/browsers.json'), true);

    foreach ($all_caps as $cap) {
        if ($cap['os'] == 'Windows' && $cap['os_version'] == 'XP') continue;
        yield $cap;
    }
});
