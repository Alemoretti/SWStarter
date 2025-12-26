<?php

namespace App\Listeners;

use App\Events\SearchPerformed;
use App\Jobs\RecomputeStatisticsJob;

class QueueStatisticsRecomputation
{
    /**
     * Handle the event.
     */
    public function handle(SearchPerformed $_event): void
    {
        // Queue statistics recomputation job
        RecomputeStatisticsJob::dispatch();
    }
}
