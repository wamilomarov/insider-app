<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $season_id
 * @property int $host_team_id
 * @property int $guest_team_id
 * @property int $host_team_score
 * @property int $guest_team_score
 * @property int $week
 * @property Team $hostTeam
 * @property Team $guestTeam
 * @property Team $winner
 * @property Season $season
 *
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Game extends Model
{
    use HasFactory;
    //    Set the max score to 4 for being more realistic
    const MAX_SCORE = 4;

    protected $fillable = [
        'season_id',
        'host_team_id',
        'guest_team_id',
        'host_team_score',
        'guest_team_score',
        'week',
        'winner_id',
    ];

    public function hostTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'host_team_id');
    }

    public function guestTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'guest_team_id');
    }

    public function season(): BelongsTo
    {
        return $this->belongsTo(Season::class);
    }

    public function winner(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'winner_id');
    }
}
