<?php

namespace App\Http\Controllers;

use App\Http\Resources\MovieResource;
use App\Services\SwapiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class MovieController extends Controller
{
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
                foreach ($movie->characters as $characterUrl) {
                    $character = $this->swapiService->getCharacter(
                        $this->extractIdFromUrl($characterUrl)
                    );
                    if ($character) {
                        $characters[] = [
                            'id' => $character->id,
                            'name' => $character->name,
                        ];
                    }
                }
            }

            // Return Inertia response for web requests
            $movieData = (new MovieResource($movie))->resolve();
            $movieData['characters'] = $characters;

            return Inertia::render('MovieDetail', [
                'movie' => $movieData,
            ]);
        } catch (\Exception $e) {
            $statusCode = 404;
            $message = 'Movie not found';

            // Extract status code from exception message
            // Format: "SWAPI request failed with status {code}: {url}"
            if (preg_match('/SWAPI request failed with status (\d+)/', $e->getMessage(), $matches)) {
                $statusCode = (int) $matches[1];
                if ($statusCode >= 500) {
                    $message = 'External API error';
                } elseif ($statusCode === 404) {
                    $message = 'Movie not found';
                }
            }

            // Return JSON for API requests
            if ($request->wantsJson()) {
                return response()->json([
                    'error' => $message,
                ], $statusCode);
            }

            // Return Inertia response with error for web requests
            return Inertia::render('MovieDetail', [
                'error' => $message,
            ])->toResponse($request)->setStatusCode($statusCode);
        }
    }

    private function extractIdFromUrl(string $url): int
    {
        if (preg_match('/\/(\d+)\/?$/', $url, $matches)) {
            return (int) $matches[1];
        }

        return 0;
    }
}
