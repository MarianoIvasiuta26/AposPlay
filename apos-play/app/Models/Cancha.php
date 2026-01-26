<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cancha extends Model
{
    protected $fillable = [
        'user_id',
        'nombre',
        'direccion',
        'precio',
        'tipo',
        'cantidad_jugadores',
    ];

    public function administrador()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

