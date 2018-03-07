<?php

return call_user_func(function () {
    $all_caps = json_decode(file_get_contents(__DIR__ . '/browsers.json'), true);

    $min_browser_versions = [
        'chrome' => '62',
        'firefox' => '56',
        'safari' => '5.1',
        'ie' => '11'
    ];

    $min_os_versions = [
        'android' => '5',
        'ios' => '8'
    ];

    foreach ($all_caps as $cap) {
        if ($cap['os'] == 'Windows' && $cap['os_version'] == 'XP') {
            continue;
        }
        if ($cap['browser'] == 'opera') {
            continue;
        }
        if (isset($min_os_versions[$cap['os']])
            && version_compare($cap['os_version'], $min_os_versions[$cap['os']], '<')
        ) {
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
