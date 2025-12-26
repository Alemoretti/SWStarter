<?php

namespace Tests\Feature;

use App\Jobs\RecomputeStatisticsJob;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class StatisticsSchedulerTest extends TestCase
{
    use RefreshDatabase;

    public function test_scheduler_dispatches_statistics_job(): void
    {
        Queue::fake();

        // Run the scheduled task
        $this->artisan('schedule:run');

        Queue::assertPushed(RecomputeStatisticsJob::class);
    }
}