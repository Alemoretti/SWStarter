<?php

namespace Tests\Unit\Models;

use App\Models\SearchStatistic;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchStatisticTest extends TestCase
{
    use RefreshDatabase;

    public function test_search_statistic_can_be_created(): void
    {
        $statistic = SearchStatistic::create([
            'top_queries' => [
                ['query' => 'luke', 'count' => 10, 'percentage' => 25.5],
                ['query' => 'yoda', 'count' => 5, 'percentage' => 12.5],
            ],
            'avg_response_time' => 150.75,
            'popular_hour' => 14,
            'computed_at' => now(),
        ]);

        $this->assertDatabaseHas('search_statistics', [
            'id' => $statistic->id,
            'popular_hour' => 14,
        ]);
    }

    public function test_search_statistic_casts_top_queries_to_array(): void
    {
        $statistic = SearchStatistic::create([
            'top_queries' => [
                ['query' => 'luke', 'count' => 10],
            ],
            'avg_response_time' => 150.75,
            'popular_hour' => 14,
            'computed_at' => now(),
        ]);

        $this->assertIsArray($statistic->top_queries);
        $this->assertEquals('luke', $statistic->top_queries[0]['query']);
    }

    public function test_search_statistic_casts_are_correct(): void
    {
        $statistic = SearchStatistic::create([
            'top_queries' => [],
            'avg_response_time' => '150.75', // String input
            'popular_hour' => '14', // String input
            'computed_at' => now(),
        ]);

        // decimal:2 cast returns string to preserve precision
        $this->assertIsString($statistic->avg_response_time);
        $this->assertEquals('150.75', $statistic->avg_response_time);
        $this->assertIsInt($statistic->popular_hour);
        $this->assertInstanceOf(\DateTimeInterface::class, $statistic->computed_at);
    }
}
