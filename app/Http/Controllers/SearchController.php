<?php

namespace App\Http\Controllers;

use App\Events\SearchPerformed;
use App\Http\Requests\SearchRequest;
use App\Http\Resources\CharacterResource;
use App\Http\Resources\MovieResource;
use App\Models\SearchQuery;
use App\Services\SwapiService;
use Illuminate\Http\JsonResponse;

class SearchController extends Controller
{
    public function __construct(
        private SwapiService $swapiService
    ) {}

    /**
     * Search for people or movies.
     */
    public function search(SearchRequest $request): JsonResponse
    {
        $startTime = microtime(true);

        $query = $request->validated()['query'];
        $type = $request->validated()['type'];

        if ($type === 'people') {
            $results = $this->swapiService->searchPeople($query);
            $response = response()->json([
                'data' => CharacterResource::collection($results),
            ]);
        } else {
            $results = $this->swapiService->searchMovies($query);
            $response = response()->json([
                'data' => MovieResource::collection($results),
            ]);
        }

        $responseTime = (int) ((microtime(true) - $startTime) * 1000);
        $resultsCount = count($results);

        SearchQuery::create([
            'query' => $query,
            'type' => $type,
            'results_count' => $resultsCount,
            'response_time_ms' => $responseTime,
        ]);

        event(new SearchPerformed($query, $type, $resultsCount));

        return $response;
    }
}
