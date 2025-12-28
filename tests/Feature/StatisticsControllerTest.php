<?php

namespace Tests\Feature;

use App\Models\SearchQuery;
use App\Models\SearchStatistic;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StatisticsControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_statistics_endpoint_returns_statistics(): void
    {
        // Create some search queries
        SearchQuery::create(['query' => 'luke', 'type' => 'people', 'results_count' => 5, 'response_time_ms' => 100]);
        SearchQuery::create(['query' => 'luke', 'type' => 'people', 'results_count' => 3, 'response_time_ms' => 150]);
        SearchQuery::create(['query' => 'yoda', 'type' => 'people', 'results_count' => 2, 'response_time_ms' => 200]);

        $response = $this->getJson('/api/v1/statistics');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'top_queries',
                    'avg_response_time',
                    'popular_hour',
                ],
            ])
            ->assertJson([
                'data' => [
                    'top_queries' => [
                        [
                            'query' => 'luke',
                            'type' => 'people',
                            'count' => 2,
                        ],
                    ],
                ],
            ]);
    }

    public function test_statistics_endpoint_returns_cached_statistics(): void
    {
        // Create cached statistics
        SearchStatistic::create([
            'top_queries' => [
                ['query' => 'test', 'type' => 'people', 'count' => 5, 'percentage' => 50.0],
            ],
            'avg_response_time' => 150.75,
            'popular_hour' => 14,
            'computed_at' => now(),
        ]);

        $response = $this->getJson('/api/v1/statistics');

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'top_queries' => [
                        ['query' => 'test', 'type' => 'people', 'count' => 5, 'percentage' => 50.0],
                    ],
                    'avg_response_time' => '150.75',
                    'popular_hour' => 14,
                ],
            ]);
    }

    public function test_statistics_endpoint_handles_no_data(): void
    {
        $response = $this->getJson('/api/v1/statistics');

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'top_queries' => [],
                    'avg_response_time' => null,
                    'popular_hour' => null,
                ],
            ]);
    }
}
