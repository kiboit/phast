<?php

return call_user_func(function () {
    $all_windows = [
        'os' => 'WINDOWS',
        'versions' => ['7', '8', '8.1', '10']
    ];

    $all_osx = [
        'os' => 'OS X',
        'versions' => ['Mavericks', 'Yosemite', 'El Capitan', 'Sierra', 'High Sierra']
    ];

    $browsers = [
        [
            'browser' => 'Edge',
            'versions' => range(14, 16),
            'platforms' => [['os' => 'WINDOWS', 'versions' => ['10']]]
        ],
        [
            'browser' => 'IE',
            'versions' => ['11'],
            'platforms' => [['os' => 'WINDOWS', 'versions' => ['10']]]
        ],
        [
            'browser' => 'Firefox',
            'versions' => range(52, 58),
            'platforms' => [
                $all_windows,
                $all_osx
            ]
        ],
        [
            'browser' => 'Chrome',
            'versions' => range(58, 64),
            'platforms' => [
                $all_windows,
                $all_osx
            ]
        ],
        [
            'browser' => 'Safari',
            'versions' => ['5.1'],
            'platforms' => [['os' => 'OS X', 'versions' => ['Snow Leopard']]]
        ],
        [
            'browser' => 'Safari',
            'versions' => ['6'],
            'platforms' => [['os' => 'OS X', 'versions' => ['Lion']]]
        ],
        [
            'browser' => 'Safari',
            'versions' => ['6.2'],
            'platforms' => [['os' => 'OS X', 'versions' => ['Mountain Lion']]]
        ],
        [
            'browser' => 'Safari',
            'versions' => ['7.1'],
            'platforms' => [['os' => 'OS X', 'versions' => ['Mavericks']]]
        ],
        [
            'browser' => 'Safari',
            'versions' => ['8'],
            'platforms' => [['os' => 'OS X', 'versions' => ['Yosemite']]]
        ],
        [
            'browser' => 'Safari',
            'versions' => ['9.1'],
            'platforms' => [['os' => 'OS X', 'versions' => ['El Capitan']]]
        ],
        [
            'browser' => 'Safari',
            'versions' => ['10.1'],
            'platforms' => [['os' => 'OS X', 'versions' => ['Sierra']]]
        ],
        [
            'browser' => 'Safari',
            'versions' => ['11'],
            'platforms' => [['os' => 'OS X', 'versions' => ['High Sierra']]]
        ]
    ];

    $mobile = [
        [
            'browser' => 'Android',
            'os' => 'android'
        ],
        [
            'browser' => 'iPhone',
            'os' => 'ios'
        ],
        [
            'browser' => 'iPad',
            'os' => 'ios'
        ]
    ];

    foreach ($browsers as $test) {
        $capabilities = [
            'browser' => $test['browser']
        ];
        foreach ($test['versions'] as $bv) {
            $capabilities['version'] = $bv;
            foreach ($test['platforms'] as $platform) {
                $capabilities['os'] = $platform['os'];
                foreach ($platform['versions'] as $osv) {
                    $capabilities['os_version'] = $osv;
                    yield $capabilities;
                }
            }
        }
    }

    foreach ($mobile as $test) {
        $test['realMobile'] = true;
        yield $test;
    }


});
