<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FavouriteMovie;

class FavouriteMoviesController extends Controller
{

    public function storeFavouriteMovie(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'movie_id' => 'required',
        ]);

        $favourite_movie = new FavouriteMovie([
            'user_id' => $request->get('user_id'),
            'movie_id' => $request->get('movie_id'),
            'isFavourited' => true,
        ]);

        $favourite_movie->save();

        return response()->json('Successfully added movie to favourites');
    }

    public function unFavouriteMovieForUser($userId, $movieId)
    {
        $favouriteMovie = FavouriteMovie::where('user_id', $userId)->where('movie_id', $movieId)->first();

        if ($favouriteMovie) {
            $favouriteMovie->delete();

            return response()->json('Successfully unfavourited the movie');
        } else {
            return response()->json('No such favourite movie found for the user');
        }
    }

    public function getFavouriteMoviesForUser($userId)
    {
        $favouriteMovies = FavouriteMovie::where('user_id', $userId)->get();
        return response()->json($favouriteMovies);
    }

    public function isMovieFavouritedByUser($userId, $movieId)
    {
        $favouriteMovie = FavouriteMovie::where('user_id', $userId)->where('movie_id', $movieId)->first();
        return response()->json(['isFavourited' => !is_null($favouriteMovie)]);
    }
}