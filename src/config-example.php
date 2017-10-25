<?php
return [
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
];
