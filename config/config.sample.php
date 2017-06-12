<?php
declare(strict_types=1);

return [
    // Used as a label
    // Can also be used as a basis for a filename (i.e. ProjectAPIv1.pdf)
    'name'    => 'Test Project',

    // Where to put the generated documentation.
    // Default: 'resources/output'
    'output'  => '/full/or-relative/path',

    // List of documentation formats to generate.
    // Default: ['pdf', 'html', 'zip']
    'formats' => ['pdf', 'html', 'zip'],

    // Needs at least one resource.
    'sources' => [
        [
            // Will be used as a label
            'name'   => 'My Spec',

            // Prefix all URLs in a spec with this string.
            // Default: none
            'prefix' => '/api/foo',

            // Paths can be relative to raml-scoop directory or absolute.
            // You can also use PHP to figure out a path.
            // Required.
            'path'   => '../relative-to/raml-scoop-dir/specification.raml',

            'exclude' => [
                '/legacy-endpoint',
                '/internal'
            ]
        ],
        [
            'name'   => 'My Git-based Spec',
            'prefix' => '/api/bar',

            // Instead of a local path you can configure a GIT repository as a source.
            // Required.
            'git'    => [
                // Any GIT repository path that can be checked out
                'uri'    => 'git@github.com:organization/repository',
                // The name of branch to check out
                // Default: master
                'branch' => 'master',
                // Path inside of the repository to take specification from.
                'path'   => '/specs/api.raml',
            ],
        ],
    ]
];
