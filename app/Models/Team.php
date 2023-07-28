<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 *
 * @property int $id
 * @property string $name
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property Collection|Game[] $gamesAtHome
 * @property Collection|Game[] $gamesAway
 * @property Collection|Game[] $games
 */
class Team extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function gamesAtHome(): HasMany
    {
        return $this->hasMany(Game::class, 'host_team_id');
    }

    public function gamesAway(): HasMany
    {
        return $this->hasMany(Game::class, 'guest_team_id');
    }
}
