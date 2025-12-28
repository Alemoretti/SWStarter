<?php

use App\Http\Controllers\CharacterController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\StatisticsController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Get routes
Route::get('/', function () {
    return Inertia::render('Search/Index');
});
Route::get('/characters/{id}', [CharacterController::class, 'show']);
Route::get('/movies/{id}', [MovieController::class, 'show']);
Route::get('/api/v1/characters/{id}', [CharacterController::class, 'show']);
Route::get('/api/v1/movies/{id}', [MovieController::class, 'show']);
Route::get('/api/v1/statistics', [StatisticsController::class, 'index']);

// Search route - accepts both GET and POST for form submissions and page refreshes
Route::match(['get', 'post'], '/api/v1/search', [SearchController::class, 'search']);
