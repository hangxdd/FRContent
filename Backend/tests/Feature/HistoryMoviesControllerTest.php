<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\HistoryMovie;
use App\Models\User;

class HistoryMoviesControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testStoreMoviesToHistoryForUser()
    {
        $user = User::factory()->create();

        $response = $this->postJson('/api/history_movies', [
            'user_id' => $user->id,
            'movie_id' => 'movie_1',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('history_movies', [
            'user_id' => $user->id,
            'movie_id' => 'movie_1',
        ]);
    }

    public function testCheckIfMovieIsAlreadyStoredInHistoryForUser()
    {
        $user = User::factory()->create();
        HistoryMovie::factory()->create([
            'user_id' => $user->id,
            'movie_id' => 'movie_1',
        ]);

        $response = $this->postJson('/api/history_movies', [
            'user_id' => $user->id,
            'movie_id' => 'movie_1',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'This movie is already in the user\'s history'
        ]);
    }

    public function testGetHistoryMoviesForUser()
    {
        $user = User::factory()->create();
        HistoryMovie::factory()->count(3)->create(['user_id' => $user->id]);

        $response = $this->getJson("/api/history_movies/{$user->id}");

        $response->assertStatus(200);
        $response->assertJsonCount(3);
    }

    public function testDeleteAllMoviesFromHistoryForUser()
    {
        $user = User::factory()->create();
        HistoryMovie::factory()->count(3)->create(['user_id' => $user->id]);

        $response = $this->deleteJson("/api/users/{$user->id}/history");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('history_movies', ['user_id' => $user->id]);
    }

    public function testDeleteMovieFromHistoryForUser()
    {
        $user = User::factory()->create();
        HistoryMovie::factory()->create([
            'user_id' => $user->id,
            'movie_id' => 'movie_1',
        ]);

        $response = $this->deleteJson("/api/history-movies/{$user->id}/movie_1");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('history_movies', [
            'user_id' => $user->id,
            'movie_id' => 'movie_1',
        ]);
    }
}
