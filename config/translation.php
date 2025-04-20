<?php

return [
    'supported_locales' => explode(',', env('SUPPORTED_LOCALES', 'en,fr,es,de,it')),
    'default_locale' => env('DEFAULT_LOCALE', 'en'),
    'cache_enabled' => env('TRANSLATION_CACHE_ENABLED', true),
    'cache_ttl' => env('TRANSLATION_CACHE_TTL', 3600), // 1 hour
];
