<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HistoryMovie; // Import the HistoryMovie model

class HistoryMoviesController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'movie_id' => 'required',
        ]);

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
}