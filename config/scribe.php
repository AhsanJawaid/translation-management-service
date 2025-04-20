<?php

return [
    'type' => 'static',
    'static' => [
        'output_path' => 'public/docs',
    ],
    'auth' => [
        'enabled' => true,
        'default' => 'bearer',
        'bearer' => [
            'name' => 'Authorization',
            'bearer_format' => 'Bearer {token}',
            'placeholder' => '{token}',
        ],
    ],
    'intro_text' => <<<INTRO
# Translation Management Service Documentation

This API allows you to manage translations for multiple locales with tagging support.
INTRO,
    'groups' => [
        'auth' => 'Authentication',
        'translations' => 'Translations Management',
    ],
    'routes' => [
        [
            'match' => [
                'domains' => ['*'],
                'prefixes' => ['api/*'],
                'versions' => ['v1'],
            ],
            'include' => [],
            'exclude' => [],
            'apply' => [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ],
                'response_calls' => [
                    'methods' => ['GET'],
                    'config' => [
                        'app.env' => 'documentation',
                        'app.debug' => false,
                    ],
                    'queryParams' => [],
                    'bodyParams' => [],
                    'fileParams' => [],
                    'cookies' => [],
                ],
            ],
        ],
    ],
];