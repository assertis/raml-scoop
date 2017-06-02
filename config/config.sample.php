<?php
declare(strict_types=1);

return [
    // Where to put the generated documentation.
    // Default: 'resources/output'
    'output'  => '/full/or-relative/path',

    // List of documentation formats to generate.
    // Default: ['pdf', 'html']
    'formats' => ['pdf', 'html'],

    // Needs at least one resource.
    'sources' => [
        [
            // Will be used as a label
            'name'   => 'My Spec',

            // Prefix all URLs in a spec with this string.
            // Default: none
            'prefix' => '/api/my',

            // Paths can be relative to raml-scoop directory or absolute.
            // You can also use PHP to figure out a path.
            // Required.
            'path'   => '../relative-to/raml-scoop-dir/specification.raml'
        ]
    ]
];
