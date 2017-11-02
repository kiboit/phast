<?php
return [

    'securityToken' => 'a-very-secure-token-that-no-one-knows',

    'retrieverMap' => [
        $_SERVER['HTTP_HOST'] => $_SERVER['DOCUMENT_ROOT']
    ],

    'cache' => [
        'cacheRoot'   => sys_get_temp_dir() . '/phast-cache-' . posix_geteuid(),
        'cacheMaxAge' => 86400 * 365,
        'garbageCollection' => [
            'maxItems'    => 100,
            'probability' => 0.1,
        ]
    ],

    'documents' => [
        'maxBufferSizeToApply' => pow(1024, 3),

        'baseUrl' => (!empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://'
            . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'],

        'filters' => [

            \Kibo\Phast\Filters\HTML\ScriptsRearrangementHTMLFilter::class => [],

            \Kibo\Phast\Filters\HTML\CSSRearrangementHTMLFilter::class => [],

            \Kibo\Phast\Filters\HTML\CSSInliningHTMLFilter::class => [],

            \Kibo\Phast\Filters\HTML\ImagesOptimizationServiceHTMLFilter::class => [
                'serviceUrl' => '/Phast/services.php?service=images'
            ],

            \Kibo\Phast\Filters\HTML\ScriptProxyServiceHTMLFilter::class => [
                'serviceUrl'     => '/Phast/services.php?service=scripts',
                'urlRefreshTime' => 7200,
                'match' => [
                    '|https://ajax\.googleapis\.com|'
                ]
            ]

        ]
    ],

    'images' => [
        'enable-cache' => true,

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
            ]
        ]
    ]
];
