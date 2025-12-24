<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DatabaseTest extends TestCase
{
    use RefreshDatabase;

    public function test_search_queries_table_exists(): void
    {
        $this->assertTrue(Schema::hasTable('search_queries'));
    }

    public function test_search_queries_table_has_correct_columns(): void
    {
        $this->assertTrue(Schema::hasColumns('search_queries', [
            'id',
            'query',
            'type',
            'results_count',
            'response_time_ms',
            'created_at',
            'updated_at',
        ]));
    }

    public function test_search_queries_table_has_indexes(): void
    {
        $connection = Schema::getConnection();
        $databaseName = $connection->getDatabaseName();

        $indexes = $connection->select(
            "SELECT INDEX_NAME 
             FROM information_schema.STATISTICS 
             WHERE TABLE_SCHEMA = ? 
             AND TABLE_NAME = 'search_queries' 
             AND INDEX_NAME != 'PRIMARY'",
            [$databaseName]
        );

        $indexNames = array_column($indexes, 'INDEX_NAME');

        $this->assertContains('search_queries_created_at_index', $indexNames);
        $this->assertContains('search_queries_type_index', $indexNames);
    }

    public function test_search_statistics_table_exists(): void
    {
        $this->assertTrue(Schema::hasTable('search_statistics'));
    }

    public function test_search_statistics_table_has_correct_columns(): void
    {
        $this->assertTrue(Schema::hasColumns('search_statistics', [
            'id',
            'top_queries',
            'avg_response_time',
            'popular_hour',
            'computed_at',
            'created_at',
            'updated_at',
        ]));
    }
}
