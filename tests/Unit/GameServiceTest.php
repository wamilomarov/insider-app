<?php

namespace Tests\Unit;

use App\Models\Season;
use App\Models\Team;
use App\Services\GameService;
use App\Services\SeasonService;
use Database\Seeders\TeamSeeder;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class GameServiceTest extends TestCase
{
    use DatabaseTransactions;

    public function testGenerationOfWeeksWorks()
    {
        $this->seed(TeamSeeder::class);

        /** @var Season $season */
        $season = Season::factory()->createOne();

        /** @var GameService $gameService */
        $gameService = app(GameService::class);

        /** @var SeasonService $seasonService */
        $seasonService = app(SeasonService::class);

        $gameService->generateWeek($season);

        $this->assertDatabaseHas('seasons', [
            'id' => $season->id,
            'is_finished' => false,
        ]);

        $this->assertDatabaseCount('games', $seasonService->maxGamesPerWeek());
    }

    public function testGenerateWeekCreatesCorrectAmountOfGames()
    {
        $this->seed(TeamSeeder::class);

        Team::factory(2)->create();

        /** @var Season $season */
        $season = Season::factory()->createOne();

        /** @var GameService $gameService */
        $gameService = app(GameService::class);

        $gameService->generateWeek($season);

        $this->assertDatabaseCount('games', 3);
    }
}
