<?php

namespace App\Services;

use App\Models\Game;
use App\Models\Season;
use App\Models\Team;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class SeasonService
{
    public function getCurrentSeason(): Season
    {
        /** @var Season $season */
        $season = Season::query()->latest()->first();
        if (is_null($season)) {
            return $this->getNewSeason();
        }
        return $this->loadSeasonRelations($season);
    }

    public function getNewSeason(): Season
    {
        /** @var Season $season */
        $season = Season::query()
            ->firstOrCreate([
                'is_started' => true,
                'is_finished' => false,
            ]);
        /** @var GameService $gameService */
        $gameService = app(GameService::class);
        $gameService->generateWeek($season);
        return $this->loadSeasonRelations($season);
    }

    public function loadSeasonRelations(Season $season): Season
    {
        return $season->loadMissing([
            'games' => [
                'guestTeam',
                'hostTeam',
            ]
        ]);
    }

    public function maxGames(): int
    {
        $teamsCount = Team::count();
        return $teamsCount * ($teamsCount - 1);
    }

    public function maxWeeks(): int
    {
        return $this->maxGames() / $this->maxGamesPerWeek();
    }

    public function maxGamesPerWeek(): int
    {
        $teamsCount = Team::count();
        return $teamsCount / 2;
    }

    public function getCurrentWeek(Season $season)
    {
        $currentWeek = $season->games()
            ->max('week');

        if (is_null($currentWeek)) {
            $currentWeek = 0;
        }
        return $currentWeek;
    }

    public function checkIfSeasonIsFinished(Season $season): void
    {
        if ($season->games()->count() === $this->maxGames()) {
            $season->is_finished = true;
            $season->save();
        }
    }

    public function getTeamsStats(Season $season): Collection
    {
        $week = $this->getCurrentWeek($season);
        return Team::query()
            ->with([
                'gamesAtHome' => fn(Builder $query) => $query->where('season_id', $season->id),
                'gamesAway' => fn(Builder $query) => $query->where('season_id', $season->id),
            ])
            ->get()
            ->map(function (Team $team) use ($week) {
                $team->setRelation('games', $team->gamesAtHome->merge($team->gamesAway));
                $result = [];
                for ($i = 1; $i <= $week; $i++) {
                    $games = $team->games->where('week', '<=', $i);
                    $wins = $games->where('winner_id', $team->id)->count();
                    $draws = $games->whereNull('winner_id')->count();
                    $result[] = [
                        'name' => $team->name,
                        'points' => $wins * 3 + $draws,
                        'games' => $games->count(),
                        'wins' => $games->where('winner_id', $team->id)->count(),
                        'draws' => $games->whereNull('winner_id')->count(),
                        'losses' => $games->where('winner_id', '!=', $team->id)->whereNotNull('winner_id')->count(),
                        'goals' => $games->sum(fn(Game $game
                        ) => $game->host_team_id === $team->id ? $game->host_team_score : $game->guest_team_score),
                        'week' => $i,
                    ];
                }
                return $result;
            });
    }
}
