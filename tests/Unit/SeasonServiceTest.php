<?php

namespace Tests\Unit;


use App\Models\Game;
use App\Models\Season;
use App\Models\Team;
use App\Services\GameService;
use App\Services\SeasonService;
use Database\Seeders\TeamSeeder;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class SeasonServiceTest extends TestCase
{
    use DatabaseTransactions;

    public function testGenerationFinishesSeasonWhenWeeksLimitReached()
    {
        $this->seed(TeamSeeder::class);

        /** @var Season $season */
        $season = Season::factory()->createOne();

        /** @var GameService $gameService */
        $gameService = app(GameService::class);

        /** @var SeasonService $seasonService */
        $seasonService = app(SeasonService::class);

        $maxWeeks = $seasonService->maxWeeks();

        for ($i = 1; $i <= $maxWeeks; $i++) {
            $gameService->generateWeek($season);

            if ($i < $maxWeeks) {
                $this->assertDatabaseHas('seasons', [
                    'id' => $season->id,
                    'is_finished' => false,
                ]);
            } else {
                $this->assertDatabaseHas('seasons', [
                    'id' => $season->id,
                    'is_finished' => true,
                ]);
            }
        }
    }

    public function testProbabilitiesAreRelativelyCorrect()
    {
        $this->seed(TeamSeeder::class);
        $teams = Team::query()->get();
        /** @var Team $teamA */
        $teamA = $teams[0];
        /** @var Team $teamB */
        $teamB = $teams[1];
        /** @var Team $teamC */
        $teamC = $teams[2];
        /** @var Team $teamD */
        $teamD = $teams[3];

        /** @var Season $season */
        $season = Season::factory()->createOne();

        Game::factory()
            ->createOne([
                'season_id' => $season->id,
                'week' => 1,
                'host_team_id' => $teamA->id,
                'guest_team_id' => $teamB->id,
                'host_team_score' => 0,
                'guest_team_score' => 0,
                'winner_id' => null,
            ]);

        Game::factory()
            ->createOne([
                'season_id' => $season->id,
                'week' => 1,
                'host_team_id' => $teamC->id,
                'guest_team_id' => $teamD->id,
                'host_team_score' => 3,
                'guest_team_score' => 0,
                'winner_id' => $teamC->id,
            ]);

        /** @var GameService $gameService */
        $gameService = app(GameService::class);

        $probabilities = $gameService->calculateProbabilities($season);

        $this->assertGreaterThan($probabilities[1][$teamA->name], $probabilities[1][$teamC->name]);
        $this->assertGreaterThan($probabilities[1][$teamB->name], $probabilities[1][$teamC->name]);
        $this->assertGreaterThan($probabilities[1][$teamD->name], $probabilities[1][$teamC->name]);
        $this->assertGreaterThan($probabilities[1][$teamD->name], $probabilities[1][$teamA->name]);
        $this->assertGreaterThan($probabilities[1][$teamD->name], $probabilities[1][$teamB->name]);
        $this->assertEquals($probabilities[1][$teamA->name], $probabilities[1][$teamB->name]);
    }

}
