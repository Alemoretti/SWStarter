<?php

namespace App\Listeners;

use App\Events\SearchPerformed;
use App\Jobs\RecomputeStatisticsJob;
use Illuminate\Support\Facades\Cache;

class QueueStatisticsRecomputation
{
    private const CACHE_KEY = 'statistics_recomputation_queued';

    /**
     * Handle the event.
     */
    public function handle(SearchPerformed $_event): void
    {
        $debounceSeconds = (int) config('statistics.job_debounce_seconds', 60);

        if (Cache::has(self::CACHE_KEY)) {
            return;
        }

        Cache::put(self::CACHE_KEY, true, now()->addSeconds($debounceSeconds));

        RecomputeStatisticsJob::dispatch()->delay(now()->addSeconds($debounceSeconds));
    }
}
