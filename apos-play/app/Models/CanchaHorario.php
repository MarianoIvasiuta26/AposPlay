<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CanchaHorario extends Model
{
    protected $fillable = [
        'cancha_id',
        'dia_id',
        'hora_apertura',
        'hora_cierre'
    ];

    public function cancha()
    {
        return $this->belongsTo(Cancha::class);
    }

    public function dia()
    {
        return $this->belongsTo(Dia::class);
    }
}
