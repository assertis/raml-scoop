<?php
declare(strict_types=1);

return [
    'name'    => 'Example API Documentation',
    'formats' => ['html', 'pdf'],
    'sources' => [
        [
            'name'   => 'Product API',
            'prefix' => '/api/product',
            'path'   => 'tests/Example/Full/api.raml',
        ],
        [
            'name'   => 'Order API',
            'prefix' => '/api/order',
            'git'    => [
                'uri'    => 'git@github.com:assertis/order-service',
                'branch' => 'master',
                'path'   => '/specs/api.raml',
            ]
        ]
    ]
];
