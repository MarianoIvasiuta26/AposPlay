<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourtAddress extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'street',
        'number',
        'city',
        'province',
        'zip_code',
        'country',
    ];

    public function courts()
    {
        return $this->hasMany(Court::class);
    }
}
