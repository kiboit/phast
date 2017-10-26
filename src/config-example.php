<?php
return [
    'documents' => [
        'maxBufferSizeToApply' => pow(1024, 3),
        'filters' => [

            \Kibo\Phast\Filters\HTML\ScriptsRearrangementHTMLFilter::class => [],

            \Kibo\Phast\Filters\HTML\CSSInliningHTMLFilter::class => [
                'baseUrl' => (!empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://'
                    . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'],
                'retrieverMap' => [
                    $_SERVER['HTTP_HOST'] => $_SERVER['DOCUMENT_ROOT']
                ]
            ],

            \Kibo\Phast\Filters\HTML\ImagesOptimizationServiceHTMLFilter::class => [
                'securityToken' => 'a-very-secure-token-that-no-one-knows',
                'baseUrl' => (!empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://'
                    . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'],
                'serviceUrl' => 'that\'s-where-the-service-is-located'
            ]

        ]
    ],

    'images' => [
        'filters' => [
            \Kibo\Phast\Filters\Image\ResizerImageFilter::class => [
                'defaultMaxWidth' => 320,
                'defaultMaxHeight' => 180
            ],

            \Kibo\Phast\Filters\Image\CompressionImageFilter::class => [
                \Kibo\Phast\Filters\Image\Image::TYPE_PNG => 9,
                \Kibo\Phast\Filters\Image\Image::TYPE_JPEG => 80
            ]
        ]
    ]
];
