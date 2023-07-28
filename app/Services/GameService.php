<?php

namespace App\Services;

use App\Models\Game;
use App\Models\Season;
use App\Models\Team;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class GameService
{
    public function generateWeek(Season $season): void
    {
        $teams = Team::all();
        /** @var SeasonService $seasonService */
        $seasonService = app(SeasonService::class);

        $week = $seasonService->getCurrentWeek($season) + 1;

        for ($i = 0; $i < $seasonService->maxGamesPerWeek(); $i++) {
            $hostTeam = $teams->random();
            $guestTeam = $teams->random();

            if ($hostTeam->id === $guestTeam->id) {
                $i--;
                continue;
            }

            if ($season->games()
                ->where('host_team_id', $hostTeam->id)
                ->where('guest_team_id', $guestTeam->id)
                ->exists()) {
                $i--;
                continue;
            }

            if ($season->games()
                ->where('week', $week)
                ->where(fn(Builder $query) => $query->whereIn('guest_team_id', [$guestTeam->id, $hostTeam->id])
                    ->orWhereIn('host_team_id', [$guestTeam->id, $hostTeam->id]))->exists()) {
                $i--;
                continue;
            }

            $this->generateGame($season, $hostTeam, $guestTeam, $week);
        }

        $seasonService->checkIfSeasonIsFinished($season);
    }

    private function generateGame(Season $season, Team $hostTeam, Team $guestTeam, int $week): void
    {
        $hostTeamScore = rand(0, Game::MAX_SCORE);
        $guestTeamScore = rand(0, Game::MAX_SCORE);

        $winnerId = null;
        if ($guestTeamScore > $hostTeamScore) {
            $winnerId = $guestTeam->id;
        }
        if ($hostTeamScore > $guestTeamScore) {
            $winnerId = $hostTeam->id;
        }

        Game::query()
            ->create([
                'season_id' => $season->id,
                'host_team_id' => $hostTeam->id,
                'guest_team_id' => $guestTeam->id,
                'host_team_score' => $hostTeamScore,
                'guest_team_score' => $guestTeamScore,
                'winner_id' => $winnerId,
                'week' => $week,
            ]);
    }

    public function calculateProbabilities(Season $season): array
    {
        $teamsCount = Team::count();
        $teams = Team::all();
        /** @var SeasonService $seasonService */
        $seasonService = app(SeasonService::class);

        $currentWeek = $seasonService->getCurrentWeek($season);

        // Set the prior probabilities (before any matches are played)
        $priorProbabilities = [];
        foreach ($teams as $team) {
            $priorProbabilities[$team->name] = round(100. / $teamsCount, 2);
        }

        // Create an array to store the probabilities for each team at the end of each week
        $probabilities = [];
        foreach ($teams as $team) {
            $probabilities[$team->name] = array_fill(0, $currentWeek, 0);
        }

        $games = $season->games()->with(['guestTeam', 'hostTeam'])->get();

        $weeklyResults = [];

        /** @var Game $game */
        foreach ($games as $game) {
            $weeklyResults[$game->week][$game->hostTeam->name] = $game->host_team_score > $game->guest_team_score ? 'win' : ($game->host_team_score === $game->guest_team_score ? 'draw' : 'loss');
            $weeklyResults[$game->week][$game->guestTeam->name] = $game->host_team_score < $game->guest_team_score ? 'win' : ($game->host_team_score === $game->guest_team_score ? 'draw' : 'loss');
        }

        // Iterate through each week
        for ($week = 1; $week <= $currentWeek; $week++) {
            // Get the results for the current week
            $results = $weeklyResults[$week];

            // Update the probabilities using Bayesian inference
            /** @var Team $team */
            foreach ($teams as $team) {
                if ($results[$team->name] == 'win') {
                    $priorProbabilities[$team->name] *= 2;
                } elseif ($results[$team->name] == 'draw') {
                    $priorProbabilities[$team->name] *= 1;
                } elseif ($results[$team->name] == 'loss') {
                    $priorProbabilities[$team->name] *= 0.5;
                }
            }

            // Normalize the probabilities
            $total_probability = array_sum($priorProbabilities);
            foreach ($teams as $team) {
                $priorProbabilities[$team->name] /= $total_probability;
            }

            // Store the probabilities for the current week
            $probabilities[$week] = $priorProbabilities;
        }

        return $probabilities;
    }

    public function getGamesByWeek(Season $season, int $week): Collection
    {
        return $season->games()
            ->where('week', $week)
            ->with(['hostTeam', 'guestTeam'])
            ->get();
    }
}
