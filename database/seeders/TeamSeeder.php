<?php

namespace Database\Seeders;

use App\Models\Team;
use Illuminate\Database\Seeder;

class TeamSeeder extends Seeder
{

    protected array $teams = [
        [
            'name' => 'Chelsea',
        ],
        [
            'name' => 'Arsenal',
        ],
        [
            'name' => 'Manchester United',
        ],
        [
            'name' => 'Liverpool',
        ]
    ];

    public function run(): void
    {
        foreach ($this->teams as $team) {
            Team::query()->updateOrCreate($team);
        }
    }
}
