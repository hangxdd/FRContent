<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HistoryMovie;

class HistoryMoviesController extends Controller
{
    public function storeHistoryMovies(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'movie_id' => 'required',
        ]);

        // Check if the movie already exists in the user's history
        $existingMovie = HistoryMovie::where('user_id', $request->get('user_id'))
            ->where('movie_id', $request->get('movie_id'))
            ->first();

        if ($existingMovie) {
            // If the movie already exists, return a response indicating this
            return response()->json('This movie is already in the user\'s history');
        }

        $history_movie = new HistoryMovie([
            'user_id' => $request->get('user_id'),
            'movie_id' => $request->get('movie_id'),
        ]);

        $history_movie->save();

        return response()->json('Successfully inserted recommended movies into the database');
    }

    public function getMoviesForUser($userId)
    {
        $movies = HistoryMovie::where('user_id', $userId)->get();

        return response()->json($movies);
    }

    public function deleteAllForUser($userId)
    {
        $deletedRows = HistoryMovie::where('user_id', $userId)->delete();

        if ($deletedRows > 0)
            return response()->json('Successfully deleted all history movies for the user');
        else
            return response()->json('No history movies found for the user');
    }

    public function deleteMovieForUser($userId, $movieId)
    {
        $deletedRows = HistoryMovie::where('user_id', $userId)->where('movie_id', $movieId)->delete();

        if ($deletedRows > 0)
            return response()->json('Successfully deleted the movie for the user');
        else
            return response()->json('No such movie found for the user');
    }

}