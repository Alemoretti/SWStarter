<?php

namespace Tests\Feature;

use App\Events\SearchPerformed;
use App\Jobs\RecomputeStatisticsJob;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class StatisticsEventListenerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Queue::fake();
        Cache::flush();
    }

    public function test_search_performed_event_queues_statistics_job(): void
    {
        event(new SearchPerformed('luke', 'people', 5));

        Queue::assertPushed(RecomputeStatisticsJob::class);
    }

    public function test_multiple_events_debounce_to_single_job(): void
    {
        // Fire multiple events in quick succession
        event(new SearchPerformed('luke', 'people', 5));
        event(new SearchPerformed('yoda', 'people', 3));
        event(new SearchPerformed('hope', 'movies', 2));

        // With debouncing, only one job should be queued
        Queue::assertPushed(RecomputeStatisticsJob::class, 1);
    }
}
