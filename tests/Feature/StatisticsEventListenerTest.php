<?php

namespace Tests\Feature;

use App\Events\SearchPerformed;
use App\Jobs\RecomputeStatisticsJob;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class StatisticsEventListenerTest extends TestCase
{
    use RefreshDatabase;

    public function test_search_performed_event_queues_statistics_job(): void
    {
        Queue::fake();

        event(new SearchPerformed('luke', 'people', 5));

        Queue::assertPushed(RecomputeStatisticsJob::class);
    }

    public function test_multiple_events_only_queue_one_job(): void
    {
        Queue::fake();

        // Fire multiple events
        event(new SearchPerformed('luke', 'people', 5));
        event(new SearchPerformed('yoda', 'people', 3));
        event(new SearchPerformed('hope', 'movies', 2));

        // Should only queue one job (debounced)
        Queue::assertPushed(RecomputeStatisticsJob::class, 1);
    }
}