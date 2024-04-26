<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HistoryMoviesController;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/history_movies', [HistoryMoviesController::class, 'store']);
Route::get('/history_movies/{userId}', [HistoryMoviesController::class, 'getMoviesForUser']);