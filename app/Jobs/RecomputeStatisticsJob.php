<?php

namespace App\Jobs;

use App\Models\SearchStatistic;
use App\Services\StatisticsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RecomputeStatisticsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     */
    public function handle(StatisticsService $statisticsService): void
    {
        $statistics = $statisticsService->compute();

        SearchStatistic::create([
            'top_queries' => $statistics['top_queries'],
            'avg_response_time' => $statistics['avg_response_time'],
            'popular_hour' => $statistics['popular_hour'],
            'computed_at' => now(),
        ]);
    }
}
