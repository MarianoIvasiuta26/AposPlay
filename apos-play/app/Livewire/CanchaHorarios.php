<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Cancha;
use App\Models\Dia;
use App\Models\CanchaHorario;

class CanchaHorarios extends Component
{
    public Cancha $cancha;
    public $dias;
    public $horarios = [];

    public $mostrarFormulario = false;



    protected function rules()
    {
        return [
            'horarios.*.apertura' => 'nullable|date_format:H:i',
            'horarios.*.cierre'   => 'nullable|date_format:H:i|after:horarios.*.apertura',
        ];
    }

    public function mount(Cancha $cancha)
    {
        $this->cancha = $cancha;
        $this->dias = Dia::all();

        foreach ($this->dias as $dia) {
            $existente = $cancha->horarios()
                ->where('dia_id', $dia->id)
                ->first();

            $this->horarios[$dia->id] = [
                'apertura' => $existente?->hora_apertura,
                'cierre'   => $existente?->hora_cierre,
            ];
        }
    }

    public function toggle()
    {
        $this->mostrarFormulario = ! $this->mostrarFormulario;
    }


    public function guardar()
    {
        $this->validate();

        // Limpia horarios previos
        $this->cancha->horarios()->delete();

        foreach ($this->horarios as $dia_id => $horario) {
            if ($horario['apertura'] && $horario['cierre']) {
                CanchaHorario::create([
                    'cancha_id'     => $this->cancha->id,
                    'dia_id'        => $dia_id,
                    'hora_apertura'=> $horario['apertura'],
                    'hora_cierre'  => $horario['cierre'],
                ]);
            }
        }

        session()->flash('success', 'Horarios guardados correctamente');
        $this->show = false;
    }

    public function render()
    {
        return view('livewire.cancha-horarios');
    }
}
