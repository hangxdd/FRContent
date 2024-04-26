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
}