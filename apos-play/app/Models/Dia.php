<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dia extends Model
{
    protected $fillable = [
        'nombre',
    ];

    public function horarios()
    {
        return $this->hasMany(CanchaHorario::class);
    }

}
