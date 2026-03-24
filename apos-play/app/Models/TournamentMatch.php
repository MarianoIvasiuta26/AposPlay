<?php

namespace App\Models;

use App\Enums\TournamentMatchStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TournamentMatch extends Model
{
    use SoftDeletes;

    protected $table = 'tournament_matches';

    protected $fillable = [
        'tournament_id',
        'round',
        'round_name',
        'home_team_id',
        'away_team_id',
        'court_id',
        'scheduled_at',
        'home_score',
        'away_score',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status'       => TournamentMatchStatus::class,
            'scheduled_at' => 'datetime',
        ];
    }

    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }

    public function homeTeam(): BelongsTo
    {
        return $this->belongsTo(TournamentTeam::class, 'home_team_id');
    }

    public function awayTeam(): BelongsTo
    {
        return $this->belongsTo(TournamentTeam::class, 'away_team_id');
    }

    public function court(): BelongsTo
    {
        return $this->belongsTo(Court::class);
    }

    public function playerStats(): HasMany
    {
        return $this->hasMany(TournamentPlayerStat::class, 'match_id');
    }
}
