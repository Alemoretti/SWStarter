<?php

return [
    'cache_ttl_minutes' => env('STATISTICS_CACHE_TTL', 5),
    'job_debounce_seconds' => env('STATISTICS_JOB_DEBOUNCE_SECONDS', 60),
];
