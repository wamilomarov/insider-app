<?php

namespace App\Http\Controllers;

use App\Models\Season;
use App\Services\GameService;
use App\Services\SeasonService;

class SeasonController extends Controller
{
    public function __construct(public SeasonService $seasonService, public GameService $gameService)
    {
    }

    public function index()
    {
        $season = $this->seasonService->getCurrentSeason();
        $probabilities = $this->gameService->calculateProbabilities($season);
        $teams = $this->seasonService->getTeamsStats($season);
        return view('season.index', [
            'season' => $season,
            'teams' => $teams->flatten(1)->groupBy('week'),
            'probabilities' => $probabilities,
        ]);
    }

    public function newSeason()
    {
        $this->seasonService->getNewSeason();
        return redirect()->route('season.index');
    }

    public function nextWeek(Season $season)
    {
        if (!$season->is_finished) {
            $this->gameService->generateWeek($season);
        }
        $season = $this->seasonService->loadSeasonRelations($season);

        $week = $this->seasonService->getCurrentWeek($season);
        $teams = $this->seasonService->getTeamsStats($season);
        $probabilities = $this->gameService->calculateProbabilities($season);
        $games = $this->gameService->getGamesByWeek($season, $week);

        return response()->json([
            'season_finished' => $season->is_finished,
            'teams_table' => view('season.components.teams-table', [
                'week' => $week,
                'teams' => $teams->flatten(1)->groupBy('week'),
            ])->render(),
            'match_results_table' => view('season.components.match-results-table', [
                'week' => $week,
                'games' => $games,
            ])->render(),
            'probabilities_table' => view('season.components.probabilities-table', [
                'week' => $week,
                'probabilities' => $probabilities,
            ])->render(),
        ]);

    }
}
