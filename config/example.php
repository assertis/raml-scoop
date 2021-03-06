<?php
declare(strict_types=1);

return [
    'name'    => 'Example API Documentation',
    'formats' => ['zip', 'html', 'pdf'],
    'sources' => [
        [
            'name'   => 'Product API',
            'prefix' => '/api/product',
            'path'   => 'tests/Example/Full/api.raml',
            'prefixes' => [
                '/orders' => '/prefix_for_orders',
                '/orders/{id}' => '/prefix_for_orders_id',
            ]
        ]
    ]
];
