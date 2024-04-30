<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HistoryMoviesController;
use App\Http\Controllers\FavouriteMoviesController;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/history_movies', [HistoryMoviesController::class, 'storeHistoryMovies']);
Route::get('/history_movies/{userId}', [HistoryMoviesController::class, 'getMoviesForUser']);
Route::delete('/users/{userId}/history', [HistoryMoviesController::class, 'deleteAllForUser']);
Route::delete('/history-movies/{userId}/{movieId}', [HistoryMoviesController::class, 'deleteMovieForUser']);

Route::post('/favourite_movies', [FavouriteMoviesController::class, 'storeFavouriteMovie']);
Route::delete('/favourite_movies/{userId}/{movieId}', [FavouriteMoviesController::class, 'unFavouriteMovieForUser']);
Route::get('/favourite_movies/{userId}', [FavouriteMoviesController::class, 'getFavouriteMoviesForUser']);
Route::get('/is_movie_favourited/{userId}/{movieId}', [FavouriteMoviesController::class, 'isMovieFavouritedByUser']);