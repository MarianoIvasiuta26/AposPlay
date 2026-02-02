<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Court;
use App\Models\Dia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CanchaHorarios extends Component
{
    public Court $cancha;
    public $dias;
    public $horarios = [];
    public $mostrarFormulario = false;

    protected $rules = [
        'horarios.*.apertura' => 'nullable|date_format:H:i',
        'horarios.*.cierre'   => 'nullable|date_format:H:i|after:horarios.*.apertura',
    ];

    public function mount(Court $cancha)
    {
        $this->cancha = $cancha;
        $this->dias = Dia::orderBy('id')->get();

        // Cargar horarios existentes desde la relación (pivote)
        // La relación 'horarios' devuelve una colección de Dias con datos pivote
        $horariosExistentes = $this->cancha->horarios;

        foreach ($this->dias as $dia) {
            // Buscar si este día ya tiene horario asignado
            $asignado = $horariosExistentes->find($dia->id);

            $this->horarios[$dia->id] = [
                'apertura' => $asignado ? substr($asignado->pivot->hora_apertura, 0, 5) : null,
                'cierre'   => $asignado ? substr($asignado->pivot->hora_cierre, 0, 5) : null,
            ];
        }
    }

    public function toggle()
    {
        $this->mostrarFormulario = ! $this->mostrarFormulario;
    }

    public function guardar()
    {
        Log::info('Guardando horarios (Pivot)', ['cancha_id' => $this->cancha->id, 'data' => $this->horarios]);
        
        try {
            $this->validate();

            DB::transaction(function () {
                $syncData = [];

                foreach ($this->horarios as $dia_id => $horario) {
                    // Si ambos campos tienen valor, preparamos para sincronizar
                    if (!empty($horario['apertura']) && !empty($horario['cierre'])) {
                        $syncData[$dia_id] = [
                            'hora_apertura' => $horario['apertura'],
                            'hora_cierre'   => $horario['cierre'],
                        ];
                    }
                }

                // Sincronizar dias. Esto eliminará los que no estén en $syncData y creará/actualizará los presentes.
                $this->cancha->horarios()->sync($syncData);
                
                Log::info('Sync completado', ['dias_sincronizados' => array_keys($syncData)]);
            });

            session()->flash('success', 'Horarios actualizados correctamente');
            $this->mostrarFormulario = false;
            
            // Recargar datos para reflejar cambios en la vista si fuera necesario (aunque Livewire lo hace)
            $this->cancha->load('horarios');

        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Error guardando pivot horarios: ' . $e->getMessage());
            session()->flash('error', 'Ocurrió un error al guardar: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.cancha-horarios');
    }
}
