<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SwapiClient
{
    /**
     * Create a new SWAPI client instance.
     *
     * @param  string  $baseUrl
     */
    public function __construct(
        private readonly string $baseUrl
    ) {}

    /**
     * Make a GET request to SWAPI.
     *
     * @param  string  $endpoint
     * @param  array<string, mixed>  $queryParams
     * @return array<string, mixed>|null
     */
    public function get(string $endpoint, array $queryParams = []): ?array
    {
        $url = rtrim($this->baseUrl, '/').'/'.ltrim($endpoint, '/');
        $response = Http::get($url, $queryParams);

        if (! $response->successful()) {
            Log::warning('SWAPI request failed', [
                'url' => $url,
                'query_params' => $queryParams,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return null;
        }

        return $response->json();
    }

    /**
     * Extract results from API response (handles both swapi.dev and swapi.info formats).
     *
     * @param  array<string, mixed>|null  $data
     * @return array<int, array<string, mixed>>
     */
    public function extractResults(?array $data): array
    {
        if (! is_array($data)) {
            return [];
        }

        // Handle both response formats:
        // swapi.dev returns: {"results": [...]}
        // swapi.info returns: [...]
        $results = isset($data[0]) && ! isset($data['results'])
            ? $data  // Direct array format (swapi.info)
            : ($data['results'] ?? []);  // Wrapped format (swapi.dev)

        return is_array($results) ? $results : [];
    }

    /**
     * Filter results by a specific field (client-side filtering).
     * This is necessary because swapi.info doesn't filter on the server side.
     *
     * @param  array<int, array<string, mixed>>  $results
     * @param  string  $query
     * @param  string  $field
     * @return array<int, array<string, mixed>>
     */
    public function filterResults(array $results, string $query, string $field): array
    {
        if (empty($results)) {
            return [];
        }

        $queryLower = strtolower(trim($query));

        return array_filter($results, function ($item) use ($queryLower, $field) {
            $value = strtolower($item[$field] ?? '');
            return str_contains($value, $queryLower);
        });
    }
}

