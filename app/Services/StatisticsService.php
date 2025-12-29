<?php

namespace App\Services;

use App\Models\SearchQuery;

class StatisticsService
{
    /**
     * Compute statistics from search queries.
     *
     * @return array<string, mixed>
     */
    public function compute(): array
    {
        $total = SearchQuery::count();

        if ($total === 0) {
            return $this->emptyStatistics();
        }

        return [
            'top_queries' => $this->getTopQueries($total),
            'avg_response_time' => $this->getAverageResponseTime(),
            'popular_hour' => $this->getPopularHour(),
        ];
    }

    /**
     * Get empty statistics structure.
     *
     * @return array<string, mixed>
     */
    private function emptyStatistics(): array
    {
        return [
            'top_queries' => [],
            'avg_response_time' => null,
            'popular_hour' => null,
        ];
    }

    /**
     * Get top 5 queries with percentages using database aggregation.
     *
     * @return array<int, array<string, mixed>>
     */
    private function getTopQueries(int $total): array
    {
        $results = SearchQuery::selectRaw('query, type, COUNT(*) as count')
            ->groupBy('query', 'type')
            ->orderByDesc('count')
            ->limit(5)
            ->get();

        return $results->map(function ($item) use ($total) {
            return [
                'query' => $item->query,
                'type' => $item->type,
                'count' => $item->count,
                'percentage' => round(($item->count / $total) * 100, 2),
            ];
        })->toArray();
    }

    /**
     * Get average response time using database aggregation.
     */
    private function getAverageResponseTime(): ?float
    {
        $avg = SearchQuery::avg('response_time_ms');

        return $avg ? round((float) $avg, 2) : null;
    }

    /**
     * Get most popular hour of day using database aggregation.
     */
    private function getPopularHour(): ?int
    {
        $result = SearchQuery::selectRaw('HOUR(created_at) as hour, COUNT(*) as count')
            ->groupBy('hour')
            ->orderByDesc('count')
            ->first();

        return $result ? (int) $result->hour : null;
    }
}
