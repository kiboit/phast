<?php
return [
    'maxBufferSizeToApply' => pow(1024, 3),
    'filters' => [

        \Kibo\Phast\Filters\ScriptsRearrangementHTMLFilter::class => [],

        \Kibo\Phast\Filters\CSSInliningHTMLFilter::class => [
            'baseURL' => 'http://' . $_SERVER['HTTP_HOST'],
            'retrieverMap' => [
                $_SERVER['HTTP_HOST'] => $_SERVER['DOCUMENT_ROOT']
            ]
        ],

        \Kibo\Phast\Filters\ImagesOptimizationServiceHTMLFilter::class => [
            'securityToken' => 'a-very-secure-token-that-no-one-knows',
            'referrerUrl' => 'http://' . $_SERVER['HTTP_HOST'],
            'serviceUrl' => 'that\'s-where-the-service-is-located'
        ]
    ]
];
