<?php

namespace App\Http\Controllers;

use App\Http\Resources\CharacterResource;
use App\Services\SwapiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class CharacterController extends Controller
{
    public function __construct(
        private SwapiService $swapiService
    ) {}

    /**
     * Get character details by ID.
     */
    public function show(Request $request, int $id): JsonResponse|InertiaResponse
    {
        try {
            $character = $this->swapiService->getCharacter($id);

            // Fetch movie details for web requests
            $movies = [];
            if (! $request->wantsJson()) {
                foreach ($character->films as $filmUrl) {
                    $movie = $this->swapiService->getMovie($filmUrl);
                    if ($movie) {
                        $movies[] = [
                            'id' => $movie->id,
                            'title' => $movie->title,
                        ];
                    }
                }
            }

            // Return JSON for API requests
            if ($request->wantsJson()) {
                return response()->json([
                    'data' => new CharacterResource($character),
                ]);
            }

            // Return Inertia response for web requests
            $characterData = (new CharacterResource($character))->resolve();
            $characterData['movies'] = $movies;

            return Inertia::render('Search/CharacterDetail', [
                'character' => $characterData,
            ]);
        } catch (\Exception $e) {
            $statusCode = 404;
            $message = 'Character not found';

            // Extract status code from exception message
            // Format: "SWAPI request failed with status {code}: {url}"
            if (preg_match('/SWAPI request failed with status (\d+)/', $e->getMessage(), $matches)) {
                $statusCode = (int) $matches[1];
                if ($statusCode >= 500) {
                    $message = 'External API error';
                } elseif ($statusCode === 404) {
                    $message = 'Character not found';
                }
            }

            // Return JSON for API requests
            if ($request->wantsJson()) {
                return response()->json([
                    'error' => $message,
                ], $statusCode);
            }

            // Return Inertia response with error for web requests
            return Inertia::render('Search/CharacterDetail', [
                'error' => $message,
            ])->toResponse($request)->setStatusCode($statusCode);
        }
    }
}
