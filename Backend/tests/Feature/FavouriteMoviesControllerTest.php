<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\FavouriteMovie;
use App\Models\User;

class FavouriteMoviesControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testStoreMovieToFavouritesForUser()
    {
        $user = User::factory()->create();

        $response = $this->postJson('/api/favourite_movies', [
            'user_id' => $user->id,
            'movie_id' => 'movie_1',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('favourite_movies', [
            'user_id' => $user->id,
            'movie_id' => 'movie_1',
            'isFavourited' => true,
        ]);
    }

    public function testUnfavouriteMovieForUser()
    {
        $user = User::factory()->create();
        $favouriteMovie = FavouriteMovie::factory()->create([
            'user_id' => $user->id,
            'movie_id' => 'movie_1',
        ]);

        $response = $this->deleteJson("/api/favourite_movies/{$user->id}/movie_1");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('favourite_movies', [
            'user_id' => $user->id,
            'movie_id' => 'movie_1',
        ]);
    }

    public function testGetFavouriteMoviesForUser()
    {
        $user = User::factory()->create();
        FavouriteMovie::factory()->count(3)->create(['user_id' => $user->id]);

        $response = $this->getJson("/api/favourite_movies/{$user->id}");

        $response->assertStatus(200);
        $response->assertJsonCount(3);
    }

    public function testIsMovieFavouritedByUser()
    {
        $user = User::factory()->create();
        FavouriteMovie::factory()->create([
            'user_id' => $user->id,
            'movie_id' => 'movie_1',
        ]);

        $response = $this->getJson("/api/is_movie_favourited/{$user->id}/movie_1");

        $response->assertStatus(200);
        $response->assertJson(['isFavourited' => true]);
    }
}
