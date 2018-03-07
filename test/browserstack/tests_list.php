<?php

return call_user_func(function () {
    $all_caps = json_decode(file_get_contents(__DIR__ . '/browsers.json'), true);

    $min_browser_versions = [
        'chrome' => '19',
        'opera' => '15',
        'firefox' => '20',
        'safari' => '5.1',
        'ie' => '11'
    ];

    foreach ($all_caps as $cap) {
        if ($cap['os'] == 'Windows' && $cap['os_version'] == 'XP') {
            continue;
        }
        if (isset($min_browser_versions[$cap['browser']])
            && version_compare($cap['browser_version'], $min_browser_versions[$cap['browser']], '<')
        ) {
            continue;
        }
        yield $cap;
    }
});
