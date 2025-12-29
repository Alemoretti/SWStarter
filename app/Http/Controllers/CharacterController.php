<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HandlesSwapiErrors;
use App\Http\Resources\CharacterResource;
use App\Services\SwapiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class CharacterController extends Controller
{
    use HandlesSwapiErrors;
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

            return Inertia::render('CharacterDetail', [
                'character' => $characterData,
            ]);
        } catch (\Throwable $e) {
            return $this->handleSwapiError($e, 'Character', 'CharacterDetail', $request);
        }
    }
}
