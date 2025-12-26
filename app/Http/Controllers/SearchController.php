<?php

namespace App\Http\Controllers;

use App\Http\Requests\SearchRequest;
use App\Http\Resources\CharacterResource;
use App\Http\Resources\MovieResource;
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
        $query = $request->validated()['query'];
        $type = $request->validated()['type'];

        if ($type === 'people') {
            $results = $this->swapiService->searchPeople($query);

            return response()->json([
                'data' => CharacterResource::collection($results),
            ]);
        }

        $results = $this->swapiService->searchMovies($query);

        return response()->json([
            'data' => MovieResource::collection($results),
        ]);
    }
}
