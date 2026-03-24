<?php

namespace App\Models;

use App\Enums\TournamentFormat;
use App\Enums\TournamentStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tournament extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'owner_id',
        'court_id',
        'sport_type',
        'format',
        'max_teams',
        'min_teams',
        'min_players',
        'max_players',
        'entry_fee',
        'prize_description',
        'registration_deadline',
        'starts_at',
        'ends_at',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'format'                => TournamentFormat::class,
            'status'                => TournamentStatus::class,
            'starts_at'             => 'date',
            'ends_at'               => 'date',
            'registration_deadline' => 'datetime',
            'entry_fee'             => 'decimal:2',
        ];
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function court(): BelongsTo
    {
        return $this->belongsTo(Court::class);
    }

    public function teams(): HasMany
    {
        return $this->hasMany(TournamentTeam::class);
    }

    public function matches(): HasMany
    {
        return $this->hasMany(TournamentMatch::class);
    }

    public function playerStats(): HasMany
    {
        return $this->hasMany(TournamentPlayerStat::class);
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', [
            TournamentStatus::OPEN->value,
            TournamentStatus::IN_PROGRESS->value,
        ]);
    }

    public function isRegistrationOpen(): bool
    {
        return $this->status === TournamentStatus::OPEN
            && $this->registration_deadline > now()
            && $this->teams()->count() < $this->max_teams;
    }
}
