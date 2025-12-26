<?php

namespace App\Http\Controllers;

use App\Events\SearchPerformed;
use App\Http\Requests\SearchRequest;
use App\Http\Resources\CharacterResource;
use App\Http\Resources\MovieResource;
use App\Models\SearchQuery;
use App\Services\SwapiService;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class SearchController extends Controller
{
    public function __construct(
        private SwapiService $swapiService
    ) {}

    /**
     * Search for people or movies.
     */
    public function search(SearchRequest $request): InertiaResponse|JsonResponse
    {
        $startTime = microtime(true);

        $query = $request->validated()['query'];
        $type = $request->validated()['type'];

        if ($type === 'people') {
            $results = $this->swapiService->searchPeople($query);
            $resources = CharacterResource::collection($results);
        } else {
            $results = $this->swapiService->searchFilms($query);
            $resources = MovieResource::collection($results);
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

        // Return Inertia response for web requests, JSON for API
        if ($request->wantsJson()) {
            return response()->json([
                'data' => $resources,
            ]);
        }

        return Inertia::render('Search/Index', [
            'query' => $query,
            'type' => $type,
            'results' => $resources->resolve(),
            'resultsCount' => $resultsCount,
        ]);
    }
}
