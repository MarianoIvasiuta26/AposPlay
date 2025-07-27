<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourtsXAdmin extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'court_id',
        'user_id',
    ];

    public function court()
    {
        return $this->belongsTo(Court::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
