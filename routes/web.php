<?php

use App\Http\Controllers\CharacterController;
use App\Http\Controllers\SearchController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/api/v1/search', [SearchController::class, 'search']);
Route::get('/api/v1/characters/{id}', [CharacterController::class, 'show']);
