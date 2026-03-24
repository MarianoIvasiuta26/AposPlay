<?php

namespace App\Models;

use App\Enums\AuditAction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class AuditLog extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'action',
        'auditable_type',
        'auditable_id',
        'old_values',
        'new_values',
        'description',
        'ip_address',
        'user_agent',
    ];

    protected function casts(): array
    {
        return [
            'action' => AuditAction::class,
            'old_values' => 'array',
            'new_values' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function auditable(): MorphTo
    {
        return $this->morphTo()->withTrashed();
    }

    public function scopeForOwner($query, User $owner)
    {
        $complexIds = $owner->complexesOwned()->pluck('id');
        $courtIds = Court::whereIn('complex_id', $complexIds)->pluck('id');

        // Allowed user_ids: the owner, their staff, and clients who reserved on their courts
        $staffIds = \DB::table('complex_staff')
            ->whereIn('complex_id', $complexIds)
            ->pluck('user_id');
        $clientIds = Reservation::whereIn('court_id', $courtIds)
            ->pluck('user_id')
            ->unique();
        $allowedUserIds = $staffIds
            ->merge($clientIds)
            ->merge([$owner->id])
            ->unique();

        return $query
            ->whereIn('user_id', $allowedUserIds)
            ->where(function ($q) use ($complexIds, $courtIds) {
                // Actions on their complexes
                $q->where(function ($sub) use ($complexIds) {
                    $sub->where('auditable_type', Complex::class)
                        ->whereIn('auditable_id', $complexIds);
                })
                // Actions on courts in their complexes
                ->orWhere(function ($sub) use ($courtIds) {
                    $sub->where('auditable_type', Court::class)
                        ->whereIn('auditable_id', $courtIds);
                })
                // Reservations on their courts
                ->orWhere(function ($sub) use ($courtIds) {
                    $sub->where('auditable_type', Reservation::class)
                        ->whereIn('auditable_id',
                            Reservation::whereIn('court_id', $courtIds)->pluck('id')
                        );
                })
                // Auth events (login/logout) of allowed users
                ->orWhere(function ($sub) {
                    $sub->where('auditable_type', User::class)
                        ->whereIn('action', ['login', 'logout']);
                });
            });
    }

    public static function auditableModelMap(): array
    {
        return [
            'Reserva' => Reservation::class,
            'Cancha' => Court::class,
            'Complejo' => Complex::class,
            'Usuario' => User::class,
            'Cupón' => Coupon::class,
            'Promoción' => Promotion::class,
            'Puntos' => LoyaltyPoint::class,
        ];
    }
}
