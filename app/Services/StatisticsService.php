<?php

namespace App\Services;

use App\Models\SearchQuery;
use Illuminate\Support\Collection;

class StatisticsService
{
    /**
     * Compute statistics from search queries.
     *
     * @return array<string, mixed>
     */
    public function compute(): array
    {
        $queries = SearchQuery::all();

        return [
            'top_queries' => $this->getTopQueries($queries),
            'avg_response_time' => $this->getAverageResponseTime($queries),
            'popular_hour' => $this->getPopularHour($queries),
        ];
    }

    /**
     * Get top 5 queries with percentages.
     * Groups by both query and type to show separate entries for people and movies.
     *
     * @return array<int, array<string, mixed>>
     */
    private function getTopQueries(Collection $queries): array
    {
        if ($queries->isEmpty()) {
            return [];
        }

        $total = $queries->count();

        // Group by both query and type to separate people and movies searches
        $queryCounts = $queries->groupBy(function ($query) {
            return $query->query.'|'.$query->type;
        })
            ->map(function ($group) use ($total) {
                $first = $group->first();

                return [
                    'query' => $first->query,
                    'type' => $first->type,
                    'count' => $group->count(),
                    'percentage' => round(($group->count() / $total) * 100, 2),
                ];
            })
            ->sortByDesc('count')
            ->take(5)
            ->values()
            ->toArray();

        return $queryCounts;
    }

    /**
     * Get average response time.
     */
    private function getAverageResponseTime(Collection $queries): ?float
    {
        if ($queries->isEmpty()) {
            return null;
        }

        $avg = $queries->avg('response_time_ms');

        return $avg ? round((float) $avg, 2) : null;
    }

    /**
     * Get most popular hour of day.
     */
    private function getPopularHour(Collection $queries): ?int
    {
        if ($queries->isEmpty()) {
            return null;
        }

        $hourCounts = $queries->groupBy(function ($query) {
            return $query->created_at->hour;
        })->map->count();

        return $hourCounts->sortDesc()->keys()->first();
    }
}
