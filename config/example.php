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
        ]
    ]
];
