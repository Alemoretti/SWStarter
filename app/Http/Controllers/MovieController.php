<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HandlesSwapiErrors;
use App\Http\Resources\MovieResource;
use App\Services\SwapiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class MovieController extends Controller
{
    use HandlesSwapiErrors;

    public function __construct(
        private SwapiService $swapiService
    ) {}

    /**
     * Get movie details by ID.
     */
    public function show(Request $request, int $id): JsonResponse|InertiaResponse
    {
        try {
            $movie = $this->swapiService->getMovieById($id);

            // Return JSON for API requests
            if ($request->wantsJson()) {
                return response()->json([
                    'data' => new MovieResource($movie),
                ]);
            }

            // Fetch character details for web requests
            $characters = [];
            if (! $request->wantsJson()) {
                $characterIds = $this->extractIdsFromUrls($movie->characters);
                $characterDtos = $this->swapiService->getCharacters($characterIds);
                $characters = array_map(function ($character) {
                    return [
                        'id' => $character->id,
                        'name' => $character->name,
                    ];
                }, $characterDtos);
            }

            // Return Inertia response for web requests
            $movieData = (new MovieResource($movie))->resolve();
            $movieData['characters'] = $characters;

            return Inertia::render('MovieDetail', [
                'movie' => $movieData,
            ]);
        } catch (\Throwable $e) {
            return $this->handleSwapiError($e, 'Movie', 'MovieDetail', $request);
        }
    }

    /**
     * Extract IDs from character URLs.
     *
     * @param  array<string>  $urls
     * @return array<int>
     */
    private function extractIdsFromUrls(array $urls): array
    {
        return array_filter(
            array_map(function ($url) {
                if (preg_match('/\/(\d+)\/?$/', $url, $matches)) {
                    return (int) $matches[1];
                }

                return null;
            }, $urls)
        );
    }
}
