<?php

return [
    'cache_ttl' => env('SWAPI_CACHE_TTL', 3600),
    'base_url' => env('SWAPI_BASE_URL', 'https://swapi.dev/api'),
    'timeout' => env('SWAPI_TIMEOUT', 10),
    'retry_times' => env('SWAPI_RETRY_TIMES', 3),
    'retry_delay' => env('SWAPI_RETRY_DELAY', 100),
];
