<?php

namespace App\Models;

use App\Enums\TournamentTeamPaymentStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class TournamentTeam extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'tournament_id',
        'name',
        'captain_id',
        'payment_status',
        'payment_id',
        'amount_paid',
    ];

    protected function casts(): array
    {
        return [
            'payment_status' => TournamentTeamPaymentStatus::class,
            'amount_paid'    => 'decimal:2',
        ];
    }

    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }

    public function captain(): BelongsTo
    {
        return $this->belongsTo(User::class, 'captain_id');
    }

    public function members(): HasMany
    {
        return $this->hasMany(TournamentTeamMember::class, 'team_id');
    }

    public function users(): HasManyThrough
    {
        return $this->hasManyThrough(
            User::class,
            TournamentTeamMember::class,
            'team_id',
            'id',
            'id',
            'user_id'
        );
    }
}
