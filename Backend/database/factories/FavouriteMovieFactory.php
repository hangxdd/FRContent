<?php

namespace Database\Factories;

use App\Models\FavouriteMovie;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class FavouriteMovieFactory extends Factory
{
    protected $model = FavouriteMovie::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'movie_id' => $this->faker->unique()->word,
            'isFavourited' => true,
        ];
    }
}
