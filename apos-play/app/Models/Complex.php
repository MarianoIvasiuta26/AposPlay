<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Complex extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'owner_id',
        'address',
        'active',
    ];

    protected function casts(): array
    {
        return [
            'active' => 'boolean',
        ];
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function staff(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'complex_staff')
            ->withPivot('created_at');
    }

    public function courts(): HasMany
    {
        return $this->hasMany(Court::class, 'complex_id');
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
}
