<?php

namespace App\Http\Controllers;

use App\DTOs\StatisticsDto;
use App\Http\Resources\StatisticsResource;
use App\Http\Responses\ApiResponse;
use App\Models\SearchStatistic;
use App\Services\StatisticsService;
use Illuminate\Http\JsonResponse;

class StatisticsController extends Controller
{
    public function __construct(
        private StatisticsService $statisticsService
    ) {}

    /**
     * Get search statistics.
     */
    public function index(): JsonResponse
    {
        // Try to get cached statistics first
        $cached = SearchStatistic::latest('computed_at')->first();

        if ($cached && $cached->computed_at->isAfter(now()->subMinutes(config('statistics.cache_ttl_minutes')))) {
            // Return cached statistics if less than 5 minutes old
            $dto = new StatisticsDto(
                topQueries: $cached->top_queries,
                avgResponseTime: $cached->avg_response_time,
                popularHour: $cached->popular_hour,
            );
        } else {
            // Compute fresh statistics
            $stats = $this->statisticsService->compute();
            $dto = new StatisticsDto(
                topQueries: $stats['top_queries'],
                avgResponseTime: $stats['avg_response_time'],
                popularHour: $stats['popular_hour'],
            );
        }

        return ApiResponse::success(new StatisticsResource($dto));
    }
}
