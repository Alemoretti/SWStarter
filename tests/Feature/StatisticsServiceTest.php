<?php

namespace Tests\Unit\Services;

use App\Models\SearchQuery;
use App\Services\StatisticsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StatisticsServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_compute_statistics_returns_top_queries(): void
    {
        // Create multiple search queries
        SearchQuery::create(['query' => 'luke', 'type' => 'people', 'results_count' => 5, 'response_time_ms' => 100]);
        SearchQuery::create(['query' => 'luke', 'type' => 'people', 'results_count' => 3, 'response_time_ms' => 150]);
        SearchQuery::create(['query' => 'yoda', 'type' => 'people', 'results_count' => 2, 'response_time_ms' => 200]);
        SearchQuery::create(['query' => 'hope', 'type' => 'movies', 'results_count' => 1, 'response_time_ms' => 120]);

        $service = new StatisticsService;
        $statistics = $service->compute();

        $this->assertIsArray($statistics['top_queries']);
        $this->assertCount(3, $statistics['top_queries']); // Top 5, but we only have 3 unique queries
        $this->assertEquals('luke', $statistics['top_queries'][0]['query']);
        $this->assertEquals(2, $statistics['top_queries'][0]['count']);
    }

    public function test_compute_statistics_calculates_average_response_time(): void
    {
        SearchQuery::create(['query' => 'test1', 'type' => 'people', 'results_count' => 1, 'response_time_ms' => 100]);
        SearchQuery::create(['query' => 'test2', 'type' => 'people', 'results_count' => 1, 'response_time_ms' => 200]);
        SearchQuery::create(['query' => 'test3', 'type' => 'people', 'results_count' => 1, 'response_time_ms' => 300]);

        $service = new StatisticsService;
        $statistics = $service->compute();

        $this->assertEquals(200.0, $statistics['avg_response_time']);
    }

    public function test_compute_statistics_returns_most_popular_hour(): void
    {
        // Create queries at different hours
        $now = now();
        SearchQuery::create([
            'query' => 'test1',
            'type' => 'people',
            'results_count' => 1,
            'response_time_ms' => 100,
            'created_at' => $now->copy()->setHour(10)
        ]);
        SearchQuery::create([
            'query' => 'test2',
            'type' => 'people',
            'results_count' => 1,
            'response_time_ms' => 100,
            'created_at' => $now->copy()->setHour(10)
        ]);
        SearchQuery::create([
            'query' => 'test3',
            'type' => 'people',
            'results_count' => 1,
            'response_time_ms' => 100,
            'created_at' => $now->copy()->setHour(14)
        ]);

        $service = new StatisticsService;
        $statistics = $service->compute();

        $this->assertEquals(10, $statistics['popular_hour']);
    }

    public function test_compute_statistics_handles_empty_data(): void
    {
        $service = new StatisticsService;
        $statistics = $service->compute();

        $this->assertIsArray($statistics['top_queries']);
        $this->assertEmpty($statistics['top_queries']);
        $this->assertNull($statistics['avg_response_time']);
        $this->assertNull($statistics['popular_hour']);
    }
}