<?php
return [

    'securityToken' => null,

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

    'servicesUrl' => '/Phast/services.php',

    'documents' => [
        'maxBufferSizeToApply' => pow(1024, 3),

        'baseUrl' => (!empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://'
            . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'],

        'filters' => [

            \Kibo\Phast\Filters\HTML\CSSRearrangementHTMLFilter::class => [],

            \Kibo\Phast\Filters\HTML\ScriptsRearrangementHTMLFilter::class => [],

            \Kibo\Phast\Filters\HTML\CSSInliningHTMLFilter::class => [],

            \Kibo\Phast\Filters\HTML\IFrameDelayedLoadingHTMLFilter::class => [],

            \Kibo\Phast\Filters\HTML\ImagesOptimizationServiceHTMLFilter::class => [],

            \Kibo\Phast\Filters\HTML\ScriptProxyServiceHTMLFilter::class => [
                'urlRefreshTime' => 7200,
                'match' => [
                    '~^https?://www\.google-analytics\.com/~',
                ]
            ],


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
            ]
        ]
    ]
];

