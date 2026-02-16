<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cancha extends Model
{
    use HasFactory;
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

    public function horarios()
    {
        return $this->hasMany(CanchaHorario::class);
    }

}

