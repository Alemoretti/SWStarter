<?php

namespace App\Services;

use App\Exceptions\SwapiException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SwapiClient
{
    /**
     * Create a new SWAPI client instance.
     */
    public function __construct(
        private readonly string $baseUrl
    ) {}

    /**
     * Make a GET request to SWAPI with timeout and retry logic.
     *
     * @param  array<string, mixed>  $queryParams
     * @return array<string, mixed>|null
     *
     * @throws SwapiException When the request fails, includes status code in message
     */
    public function get(string $endpoint, array $queryParams = []): ?array
    {
        $url = rtrim($this->baseUrl, '/').'/'.ltrim($endpoint, '/');
        $timeout = config('swapi.timeout', 10);
        $retryTimes = config('swapi.retry_times', 3);
        $retryDelay = config('swapi.retry_delay', 100);

        try {
            $response = Http::timeout($timeout)
                ->retry($retryTimes, $retryDelay, function ($exception, $request) {
                    return $this->shouldRetry($exception);
                })
                ->get($url, $queryParams);

            if (! $response->successful()) {
                $statusCode = $response->status();

                Log::warning('SWAPI request failed', [
                    'url' => $url,
                    'query_params' => $queryParams,
                    'status' => $statusCode,
                    'body' => $response->body(),
                ]);

                throw new SwapiException(
                    "SWAPI request failed: {$url}",
                    $statusCode
                );
            }

            return $response->json();
        } catch (\Illuminate\Http\Client\RequestException $e) {
            $statusCode = $e->response?->status() ?? 500;

            Log::warning('SWAPI request failed', [
                'url' => $url,
                'query_params' => $queryParams,
                'status' => $statusCode,
                'exception' => get_class($e),
                'message' => $e->getMessage(),
            ]);

            throw new SwapiException(
                "SWAPI request failed: {$url}",
                $statusCode
            );
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::warning('SWAPI connection failed', [
                'url' => $url,
                'query_params' => $queryParams,
                'exception' => get_class($e),
                'message' => $e->getMessage(),
                'timeout' => $timeout,
            ]);

            throw new SwapiException(
                "SWAPI connection failed: {$url}",
                503
            );
        }
    }

    /**
     * Determine if a request should be retried based on the exception.
     */
    private function shouldRetry(\Throwable $exception): bool
    {
        if ($exception instanceof \Illuminate\Http\Client\ConnectionException) {
            return true;
        }

        if ($exception instanceof \Illuminate\Http\Client\RequestException) {
            $statusCode = $exception->response?->status();

            if ($statusCode === null) {
                return true;
            }

            return $statusCode >= 500 || $statusCode === 429;
        }

        return false;
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
