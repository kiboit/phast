<?php
return [

    'securityToken' => null,

    'retrieverMap' => [
        $_SERVER['HTTP_HOST'] => $_SERVER['DOCUMENT_ROOT']
    ],

    'cache' => [
        'cacheRoot'   => sys_get_temp_dir() . '/phast-cache-' . posix_geteuid(),
        'garbageCollection' => [
            'maxItems'    => 100,
            'probability' => 0.1,
            'maxAge' => 86400 * 365
        ]
    ],

    'servicesUrl' => '/phast.php',

    'documents' => [
        'maxBufferSizeToApply' => pow(1024, 3),

        'baseUrl' => (!empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://'
            . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'],

        'filters' => [

            \Kibo\Phast\Filters\HTML\ImagesOptimizationServiceHTMLFilter::class => [
                'rewriteFormat' => \Kibo\Phast\Services\ServiceRequest::FORMAT_PATH
            ],

            \Kibo\Phast\Filters\HTML\CSSInliningHTMLFilter::class => [
                'urlRefreshTime' => 7200,
                'whitelist' => [
                    '~^https?://' . preg_quote($_SERVER['HTTP_HOST'], '~') . '/~',
                    '~^https?://fonts\.googleapis\.com/css~' => [
                        'ieCompatible' => false
                    ],
                    '~^https?://ajax\.googleapis\.com/ajax/libs/jqueryui/~'
                ]
            ],

            \Kibo\Phast\Filters\HTML\CSSImagesOptimizationServiceHTMLFilter::class => [],

            \Kibo\Phast\Filters\HTML\CSSOptimizingHTMLFilter::class => [],

            \Kibo\Phast\Filters\HTML\CSSDeferHTMLFilter::class => [],

            \Kibo\Phast\Filters\HTML\IFrameDelayedLoadingHTMLFilter::class => [],

            \Kibo\Phast\Filters\HTML\ScriptsRearrangementHTMLFilter::class => [],

            \Kibo\Phast\Filters\HTML\ScriptProxyServiceHTMLFilter::class => [
                'urlRefreshTime' => 7200,
                'match' => [
                    '~^https?://' . preg_quote($_SERVER['HTTP_HOST'], '~') . '/~',
                    '~^https?://(ssl|www)\.google-analytics\.com/~',
                    '~^https?://static\.hotjar\.com/~'
                ]
            ],

            \Kibo\Phast\Filters\HTML\ScriptsDeferHTMLFilter::class => [],

        ]
    ],

    'images' => [
        'enable-cache' => true,

        'whitelist' => [
            '~^https?://' . preg_quote($_SERVER['HTTP_HOST'], '~') . '/~',
            '~^https?://ajax\.googleapis\.com/ajax/libs/jqueryui/~'
        ],

        'filters' => [
            \Kibo\Phast\Filters\Image\ResizerImageFilter::class => [
                'defaultMaxWidth'  => 1920 * 2,
                'defaultMaxHeight' => 1080 * 2
            ],

            \Kibo\Phast\Filters\Image\CompressionImageFilter::class => [
                \Kibo\Phast\Filters\Image\Image::TYPE_PNG  =>  9,
                \Kibo\Phast\Filters\Image\Image::TYPE_JPEG => 80
            ],

            \Kibo\Phast\Filters\Image\WEBPEncoderImageFilter::class => [
                'enabled'     => function_exists('imagewebp'),
                'compression' => 80
            ],

            \Kibo\Phast\Filters\Image\PNGQuantCompressionImageFilter::class => [
                'enabled' => @file_exists('/usr/bin/pngquant'),
                'cmdpath' => '/usr/bin/pngquant',
                'quality' => '50-85'
            ],

            \Kibo\Phast\Filters\Image\JPEGTransEnhancerImageFilter::class => [
                'enabled' => @file_exists('/usr/bin/jpegtran'),
                'cmdpath' => '/usr/bin/jpegtran'
            ]
        ]
    ],

    'diagnostics' => [

        'logWriter' => [
            'class' => \Kibo\Phast\Diagnostics\LogWriters\CompositeLogWriter::class,
            'logWriters' => [
                [
                    'class' => \Kibo\Phast\Diagnostics\LogWriters\PHPErrorLogWriter::class,
                    'levelMask' =>
                          \Kibo\Phast\Diagnostics\LogLevel::EMERGENCY
                        | \Kibo\Phast\Diagnostics\LogLevel::ALERT
                        | \Kibo\Phast\Diagnostics\LogLevel::CRITICAL
                        | \Kibo\Phast\Diagnostics\LogLevel::ERROR
                        | \Kibo\Phast\Diagnostics\LogLevel::WARNING
                ],

                [
                    'class' => \Kibo\Phast\Diagnostics\LogWriters\JSONLFileLogWriter::class,
                    'logRoot' => sys_get_temp_dir() . '/phast-logs'
                ]
            ]
        ]
    ]
];

