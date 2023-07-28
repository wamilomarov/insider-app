<?php

namespace Database\Factories;

use App\Models\Game;
use App\Models\Season;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class GameFactory extends Factory
{
    protected $model = Game::class;

    public function definition(): array
    {
        return [
            'host_team_score' => $this->faker->numberBetween(0, Game::MAX_SCORE),
            'guest_team_score' => $this->faker->numberBetween(0, Game::MAX_SCORE),
            'week' => $this->faker->randomNumber(),

            'host_team_id' => fn() => Team::factory(),
            'guest_team_id' => fn() => Team::factory(),
            'season_id' => fn() => Season::factory(),
            'winner_id' => null,
        ];
    }
}
