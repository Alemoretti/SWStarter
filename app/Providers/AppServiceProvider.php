<?php

namespace App\Providers;

use App\Services\SwapiClient;
use App\Services\SwapiService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(SwapiService::class, function ($app) {
            $baseUrl = config('services.swapi.base_url', 'https://swapi.dev/api');
            $client = new SwapiClient($baseUrl);

            return new SwapiService($client);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
