<?php

namespace Tests\Unit\Models;

use App\Models\SearchQuery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchQueryTest extends TestCase
{
    use RefreshDatabase;

    public function test_search_query_can_be_created(): void
    {
        $query = SearchQuery::create([
            'query' => 'luke',
            'type' => 'people',
            'results_count' => 5,
            'response_time_ms' => 150,
        ]);

        $this->assertDatabaseHas('search_queries', [
            'id' => $query->id,
            'query' => 'luke',
            'type' => 'people',
            'results_count' => 5,
            'response_time_ms' => 150,
        ]);
    }

    public function test_search_query_has_fillable_attributes(): void
    {
        $query = new SearchQuery([
            'query' => 'yoda',
            'type' => 'people',
            'results_count' => 3,
        ]);

        $this->assertEquals('yoda', $query->query);
        $this->assertEquals('people', $query->type);
        $this->assertEquals(3, $query->results_count);
    }

    public function test_search_query_casts_are_correct(): void
    {
        $query = SearchQuery::create([
            'query' => 'test',
            'type' => 'movies',
            'results_count' => '10', // String input
            'response_time_ms' => '200', // String input
        ]);

        $this->assertIsInt($query->results_count);
        $this->assertIsInt($query->response_time_ms);
        $this->assertEquals(10, $query->results_count);
        $this->assertEquals(200, $query->response_time_ms);
    }
}
