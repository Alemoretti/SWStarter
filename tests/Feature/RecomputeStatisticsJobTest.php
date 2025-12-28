<?php

namespace Tests\Unit\Jobs;

use App\Jobs\RecomputeStatisticsJob;
use App\Models\SearchQuery;
use App\Models\SearchStatistic;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RecomputeStatisticsJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_job_computes_and_saves_statistics(): void
    {
        // Create search queries
        SearchQuery::create(['query' => 'luke', 'type' => 'people', 'results_count' => 5, 'response_time_ms' => 100]);
        SearchQuery::create(['query' => 'luke', 'type' => 'people', 'results_count' => 3, 'response_time_ms' => 150]);

        $job = new RecomputeStatisticsJob;
        app()->call([$job, 'handle']);

        $statistic = SearchStatistic::latest('computed_at')->first();

        $this->assertNotNull($statistic);
        $this->assertIsArray($statistic->top_queries);
        $this->assertCount(1, $statistic->top_queries);
        $this->assertEquals('luke', $statistic->top_queries[0]['query']);
        $this->assertEquals('people', $statistic->top_queries[0]['type']);
        $this->assertEquals(125.0, $statistic->avg_response_time);
    }

    public function test_job_creates_new_statistic_record(): void
    {
        SearchQuery::create(['query' => 'test', 'type' => 'people', 'results_count' => 1, 'response_time_ms' => 200]);

        $initialCount = SearchStatistic::count();

        $job = new RecomputeStatisticsJob;
        app()->call([$job, 'handle']);

        $this->assertEquals($initialCount + 1, SearchStatistic::count());
    }

    public function test_job_handles_empty_data(): void
    {
        $job = new RecomputeStatisticsJob;
        app()->call([$job, 'handle']);

        $statistic = SearchStatistic::latest('computed_at')->first();

        $this->assertNotNull($statistic);
        $this->assertEmpty($statistic->top_queries);
        $this->assertNull($statistic->avg_response_time);
        $this->assertNull($statistic->popular_hour);
    }
}
