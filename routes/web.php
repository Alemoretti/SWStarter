<?php

use App\Http\Controllers\CharacterController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\StatisticsController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::post('/api/v1/search', [SearchController::class, 'search']);
Route::get('/api/v1/characters/{id}', [CharacterController::class, 'show']);
Route::get('/api/v1/movies/{id}', [MovieController::class, 'show']);
Route::get('/api/v1/statistics', [StatisticsController::class, 'index']);
Route::get('/test', function () {
    return Inertia::render('Test');
});
