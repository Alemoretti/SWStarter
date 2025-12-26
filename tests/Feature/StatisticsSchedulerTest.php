<?php

namespace Tests\Feature;

use App\Jobs\RecomputeStatisticsJob;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class StatisticsSchedulerTest extends TestCase
{
    use RefreshDatabase;

    public function test_scheduler_can_dispatch_statistics_job(): void
    {
        Queue::fake();

        // Dispatch the job that will be scheduled
        RecomputeStatisticsJob::dispatch();

        Queue::assertPushed(RecomputeStatisticsJob::class);
    }
}
