<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Court;
use App\Models\Dia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CourtSchedules extends Component
{
    public Court $court;
    public $days;
    public $schedules = [];
    public $showForm = false;
    public bool $standalone = false;

    protected $rules = [
        'schedules.*.active'       => 'boolean',
        'schedules.*.start_time_1' => 'nullable|date_format:H:i|required_with:schedules.*.end_time_1',
        'schedules.*.end_time_1'   => 'nullable|date_format:H:i|required_with:schedules.*.start_time_1|after:schedules.*.start_time_1',
        'schedules.*.start_time_2' => 'nullable|date_format:H:i|required_with:schedules.*.end_time_2',
        'schedules.*.end_time_2'   => 'nullable|date_format:H:i|required_with:schedules.*.start_time_2|after:schedules.*.start_time_2',
    ];

    protected $messages = [
        'schedules.*.start_time_1.date_format'   => 'El formato de hora de apertura (turno mañana) debe ser HH:MM.',
        'schedules.*.start_time_1.required_with' => 'Debe ingresar la hora de apertura del turno mañana.',
        'schedules.*.end_time_1.date_format'     => 'El formato de hora de cierre (turno mañana) debe ser HH:MM.',
        'schedules.*.end_time_1.required_with'   => 'Debe ingresar la hora de cierre del turno mañana.',
        'schedules.*.end_time_1.after'           => 'El cierre del turno mañana debe ser posterior a la apertura.',
        'schedules.*.start_time_2.date_format'   => 'El formato de hora de apertura (turno tarde) debe ser HH:MM.',
        'schedules.*.start_time_2.required_with' => 'Debe ingresar la hora de apertura del turno tarde.',
        'schedules.*.end_time_2.date_format'     => 'El formato de hora de cierre (turno tarde) debe ser HH:MM.',
        'schedules.*.end_time_2.required_with'   => 'Debe ingresar la hora de cierre del turno tarde.',
        'schedules.*.end_time_2.after'           => 'El cierre del turno tarde debe ser posterior a la apertura.',
    ];

    public function mount(Court $court)
    {
        $this->court = $court;
        $this->standalone = request()->routeIs('court.schedules');

        // Verify ownership
        $esPropia = $this->court->courtsXAdmin()->where('user_id', auth()->id())->exists();
        if (!$esPropia) {
            abort(403);
        }

        // If standalone (accessed via route), show form immediately
        if ($this->standalone) {
            $this->showForm = true;
        }

        $this->days = Dia::orderBy('id')->get();

        // Load existing schedules from pivot
        $existingSchedules = $this->court->schedules;

        foreach ($this->days as $day) {
            // Find if this day has assigned schedule
            $assigned = $existingSchedules->find($day->id);

            $this->schedules[$day->id] = [
                'active'       => $assigned ? true : false,
                'start_time_1' => $assigned ? substr($assigned->pivot->start_time_1, 0, 5) : null,
                'end_time_1'   => $assigned ? substr($assigned->pivot->end_time_1, 0, 5) : null,
                'start_time_2' => $assigned ? substr($assigned->pivot->start_time_2, 0, 5) : null,
                'end_time_2'   => $assigned ? substr($assigned->pivot->end_time_2, 0, 5) : null,
            ];
        }
    }

    public function toggle()
    {
        $this->showForm = ! $this->showForm;
    }

    public function save()
    {
        Log::info('Saving schedules (Pivot Multi-shift)', ['court_id' => $this->court->id, 'data' => $this->schedules]);

        try {
            $this->validate();

            // Verificar que cada día activo tenga al menos un turno completo
            $hasError = false;
            foreach ($this->schedules as $dayId => $schedule) {
                if (!empty($schedule['active']) && $schedule['active'] === true) {
                    $hasShift1 = !empty($schedule['start_time_1']) && !empty($schedule['end_time_1']);
                    $hasShift2 = !empty($schedule['start_time_2']) && !empty($schedule['end_time_2']);
                    if (!$hasShift1 && !$hasShift2) {
                        $this->addError("schedules.{$dayId}.start_time_1", 'Debe completar al menos un turno para este día.');
                        $hasError = true;
                    }
                }
            }
            if ($hasError) return;

            DB::transaction(function () {
                $syncData = [];

                foreach ($this->schedules as $day_id => $schedule) {
                    if (!empty($schedule['active']) && $schedule['active'] === true) {
                        $hasShift1 = !empty($schedule['start_time_1']) && !empty($schedule['end_time_1']);
                        $hasShift2 = !empty($schedule['start_time_2']) && !empty($schedule['end_time_2']);

                        if ($hasShift1 || $hasShift2) {
                            $syncData[$day_id] = [
                                'start_time_1' => $hasShift1 ? $schedule['start_time_1'] : null,
                                'end_time_1'   => $hasShift1 ? $schedule['end_time_1'] : null,
                                'start_time_2' => $hasShift2 ? $schedule['start_time_2'] : null,
                                'end_time_2'   => $hasShift2 ? $schedule['end_time_2'] : null,
                            ];
                        }
                    }
                }

                // Sync pivot data
                $this->court->schedules()->sync($syncData);
                
                Log::info('Sync completed', ['synced_days' => array_keys($syncData)]);
            });

            session()->flash('success', 'Horarios guardados correctamente');

            // Reload relationship
            $this->court->load('schedules');

            if ($this->standalone) {
                return $this->redirect(route('canchas'), navigate: true);
            }

            $this->showForm = false;

        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Error saving schedules: ' . $e->getMessage());
            session()->flash('error', 'Error al guardar horarios: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $view = view('livewire.court-schedules');

        if ($this->standalone) {
            return $view->layout('components.layouts.app', ['title' => 'Horarios - ' . $this->court->name]);
        }

        return $view;
    }
}
