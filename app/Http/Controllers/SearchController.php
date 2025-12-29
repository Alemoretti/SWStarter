<?php

namespace App\Http\Controllers;

use App\Events\SearchPerformed;
use App\Http\Requests\SearchRequest;
use App\Http\Resources\CharacterResource;
use App\Http\Resources\MovieResource;
use App\Http\Responses\ApiResponse;
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

        $validated = $request->validated();
        $query = $validated['query'];
        $type = $validated['type'];
        $page = $validated['page'] ?? 1;
        $perPage = config('search.per_page');

        if ($type === 'people') {
            $results = $this->swapiService->searchPeople($query);
        } else {
            $results = $this->swapiService->searchFilms($query);
        }

        $responseTime = (int) ((microtime(true) - $startTime) * 1000);
        $totalResults = count($results);
        $totalPages = (int) ceil($totalResults / $perPage);

        // Paginate results
        $offset = ($page - 1) * $perPage;
        $paginatedResults = array_slice($results, $offset, $perPage);

        // Create resources from paginated results
        if ($type === 'people') {
            $resources = CharacterResource::collection($paginatedResults);
        } else {
            $resources = MovieResource::collection($paginatedResults);
        }

        SearchQuery::create([
            'query' => $query,
            'type' => $type,
            'results_count' => $totalResults,
            'response_time_ms' => $responseTime,
        ]);

        event(new SearchPerformed($query, $type, $totalResults));

        // Return Inertia response for web requests, JSON for API
        if ($request->wantsJson()) {
            return ApiResponse::success($resources, [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $totalResults,
                'total_pages' => $totalPages,
            ]);
        }

        return Inertia::render('Index', [
            'query' => $query,
            'type' => $type,
            'results' => $resources->resolve(),
            'resultsCount' => $totalResults,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $totalResults,
                'total_pages' => $totalPages,
            ],
        ]);
    }
}
