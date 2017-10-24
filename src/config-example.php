<?php
return [
    'maxBufferSizeToApply' => pow(1024, 3),
    'filters' => [

        \Kibo\Phast\Filters\ScriptsRearrangementHTMLFilter::class => [],

        \Kibo\Phast\Filters\CSSInliningHTMLFilter::class => [
            'baseURL' => 'http://phast.dev'
        ],

        \Kibo\Phast\Filters\ImagesOptimizationServiceHTMLFilter::class => [
            'securityToken' => 'a-very-secure-token-that-no-one-knows',
            'referrerUrl' => 'http://phast.dev/',
            'serviceUrl' => 'that\'s-where-the-service-is-located'
        ]
    ]
];
