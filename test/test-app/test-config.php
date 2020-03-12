<?php
return [
    'documents' => [
        'filters' => [
            Kibo\Phast\Filters\HTML\ScriptsProxyService\Filter::class => [
                'match' => [
                    '~https?://ajax\.googleapis\.com/~',
                ],
            ],
        ],
    ],
];
