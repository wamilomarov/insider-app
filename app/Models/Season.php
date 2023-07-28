<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property boolean $is_started
 * @property boolean $is_finished
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Game[]|Collection $games
 */
class Season extends Model
{
    use HasFactory;
    protected $fillable = [
        'is_started',
        'is_finished',
    ];

    protected $casts = [
        'is_started' => 'boolean',
        'is_finished' => 'boolean',
    ];

    public function games(): HasMany
    {
        return $this->hasMany(Game::class)->orderByDesc('week')->orderByDesc('id');
    }
}
