<?php

use Kibo\Phast\Common\System;
use Kibo\Phast\HTTP\Request;
use Kibo\Phast\HTTP\RequestsHTTPClient;

return [

    'securityToken' => null,

    'retrieverMap' => [
        $_SERVER['HTTP_HOST'] => Request::fromGlobals()->getDocumentRoot()
    ],

    'httpClient' => function () {
        Requests::set_certificate_path(__DIR__ . '/../../certificates/mozilla-cacert.pem');
        return new RequestsHTTPClient();
    },

    'cache' => [
        'cacheRoot' => sys_get_temp_dir() . '/phast-cache-' . (new System())->getUserId(),
        'shardingDepth' => 1,
        'garbageCollection' => [
            'maxItems'    => 100,
            'probability' => 0.1,
            'maxAge' => 86400 * 365
        ],
        'diskCleanup' => [
            'maxSize' => 100 * pow(1024, 3),
            'probability' => 0.02,
            'portionToFree' => 0.5
        ]
    ],

    'servicesUrl' => '/phast.php',

    'serviceRequestFormat' => \Kibo\Phast\Services\ServiceRequest::FORMAT_PATH,

    'optimizeHTMLDocumentsOnly' => true,

    'outputServerSideStats' => true,

    'documents' => [
        'maxBufferSizeToApply' => pow(1024, 3),

        'baseUrl' => (!empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://'
            . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'],

        'filters' => [

            \Kibo\Phast\Filters\HTML\CommentsRemoval\Filter::class => [],

            \Kibo\Phast\Filters\HTML\BaseURLSetter\Filter::class => [],

            \Kibo\Phast\Filters\HTML\ImagesOptimizationService\Tags\Filter::class => [],

            \Kibo\Phast\Filters\HTML\CSSInlining\Filter::class => [
                'optimizerSizeDiffThreshold' => 1024,
                'whitelist' => [
                    '~^https?://' . preg_quote($_SERVER['HTTP_HOST'], '~') . '/~',
                    '~^https?://fonts\.googleapis\.com/css~' => [
                        'ieCompatible' => false
                    ],
                    '~^https?://ajax\.googleapis\.com/ajax/libs/jqueryui/~',
                    '~^https?://maxcdn\.bootstrapcdn\.com/[^?#]*\.css~'
                ]
            ],

            \Kibo\Phast\Filters\HTML\ImagesOptimizationService\CSS\Filter::class => [],

            \Kibo\Phast\Filters\HTML\DelayedIFrameLoading\Filter::class => [],

            \Kibo\Phast\Filters\HTML\ScriptsRearrangement\Filter::class => [],

            \Kibo\Phast\Filters\HTML\ScriptsProxyService\Filter::class => [
                'urlRefreshTime' => 7200,
                'match' => [
                    '~^https?://' . preg_quote($_SERVER['HTTP_HOST'], '~') . '/~',
                    '~^https?://(ssl|www)\.google-analytics\.com/(analytics|ga)\.js~',
                    '~^https?://www\.googletagmanager\.com/~',
                    '~^https?://www\.googleadservices\.com/~',
                    '~^https?://pixel\.adcrowd\.com/~',
                    '~^https?://connect\.facebook\.net/~',
                    '~^https?://static\.hotjar\.com/~',
                    '~^https?://v2\.zopim\.com/~',
                    '~^https?://maps\.googleapis\.com/maps/api/js~',
                    '~^https?://stats\.g\.doubleclick\.net/dc\.js$~'
                ]
            ],

            \Kibo\Phast\Filters\HTML\Diagnostics\Filter::class => [
                'enabled' => 'diagnostics'
            ],

            \Kibo\Phast\Filters\HTML\ScriptsDeferring\Filter::class => [],

            \Kibo\Phast\Filters\HTML\PhastScriptsCompiler\Filter::class => []

        ]
    ],

    'images' => [
        'enable-cache' => 'imgcache',

        'api-mode' => false,

        'maxImageInliningSize' => 512,

        'whitelist' => [
            '~^https?://' . preg_quote($_SERVER['HTTP_HOST'], '~') . '/[^#?]*\.(jpe?g|gif|png)~i',
            '~^https?://ajax\.googleapis\.com/ajax/libs/jqueryui/~'
        ],

        'filters' => [
            \Kibo\Phast\Filters\Image\Resizer\Filter::class => [
                'defaultMaxWidth'  => 1920 * 2,
                'defaultMaxHeight' => 1080 * 2
            ],

            \Kibo\Phast\Filters\Image\Compression\Filter::class => [
                \Kibo\Phast\Filters\Image\Image::TYPE_PNG  =>  9,
                \Kibo\Phast\Filters\Image\Image::TYPE_JPEG => 80
            ],

            \Kibo\Phast\Filters\Image\WEBPEncoder\Filter::class => [
                'compression' => 80
            ],

            \Kibo\Phast\Filters\Image\PNGQuantCompression\Filter::class => [
                'quality' => '50-85'
            ],

            \Kibo\Phast\Filters\Image\JPEGTransEnhancer\Filter::class => [],

//            \Kibo\Phast\Filters\Image\ImageAPIClient\Filter::class => [
//                'api-url' => 'http://optimize.phast.io/?service=images',
//                'host-name' => $_SERVER['HTTP_HOST'],
//                'request-uri' => $_SERVER['REQUEST_URI'],
//                'plugin-version' => 'phast-core-1.0'
//            ]
        ]
    ],

    'styles' => [

        'filters' => [
            \Kibo\Phast\Filters\CSS\ImportsStripper\Filter::class => [],
            \Kibo\Phast\Filters\CSS\CSSMinifier\Filter::class => [],
            \Kibo\Phast\Filters\CSS\CSSURLRewriter\Filter::class => [],
            \Kibo\Phast\Filters\CSS\ImageURLRewriter\Filter::class => [
                'maxImageInliningSize' => 512
            ],
            \Kibo\Phast\Filters\CSS\FontSwap\Filter::class => []
        ]
    ],

    'logging' => [
        'logWriters' => [
            [
                'class' => \Kibo\Phast\Logging\LogWriters\PHPError\Writer::class,
                'levelMask' =>
                    \Kibo\Phast\Logging\LogLevel::EMERGENCY
                    | \Kibo\Phast\Logging\LogLevel::ALERT
                    | \Kibo\Phast\Logging\LogLevel::CRITICAL
                    | \Kibo\Phast\Logging\LogLevel::ERROR
                    | \Kibo\Phast\Logging\LogLevel::WARNING
            ],
            [
                'enabled' => 'diagnostics',
                'class' => \Kibo\Phast\Logging\LogWriters\JSONLFile\Writer::class,
                'logRoot' => sys_get_temp_dir() . '/phast-logs'
            ]
        ]
    ],

    'switches' => [
        'phast' => true,
        'diagnostics' => false
    ],

    'scripts' => [
        'removeLicenseHeaders' => false
    ]
];

