<?php

namespace Database\Factories;

use App\Models\HistoryMovie;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class HistoryMovieFactory extends Factory
{
    protected $model = HistoryMovie::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'movie_id' => $this->faker->unique()->word,
        ];
    }
}
