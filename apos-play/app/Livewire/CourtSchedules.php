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

    protected $rules = [
        'schedules.*.active'       => 'boolean',
        'schedules.*.start_time_1' => 'nullable|required_if:schedules.*.active,true|date_format:H:i',
        'schedules.*.end_time_1'   => 'nullable|required_if:schedules.*.active,true|date_format:H:i|after:schedules.*.start_time_1',
        'schedules.*.start_time_2' => 'nullable|date_format:H:i',
        'schedules.*.end_time_2'   => 'nullable|required_with:schedules.*.start_time_2|date_format:H:i|after:schedules.*.start_time_2',
    ];

    public function mount(Court $court)
    {
        $this->court = $court;
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

            DB::transaction(function () {
                $syncData = [];

                foreach ($this->schedules as $day_id => $schedule) {
                    // Only sync if active is true
                    if (!empty($schedule['active']) && $schedule['active'] === true) {
                         // Essential checks for first shift
                        if (!empty($schedule['start_time_1']) && !empty($schedule['end_time_1'])) {
                            $syncData[$day_id] = [
                                'start_time_1' => $schedule['start_time_1'],
                                'end_time_1'   => $schedule['end_time_1'],
                                'start_time_2' => !empty($schedule['start_time_2']) ? $schedule['start_time_2'] : null,
                                'end_time_2'   => !empty($schedule['end_time_2']) ? $schedule['end_time_2'] : null,
                            ];
                        }
                    }
                }

                // Sync pivot data
                $this->court->schedules()->sync($syncData);
                
                Log::info('Sync completed', ['synced_days' => array_keys($syncData)]);
            });

            session()->flash('success', 'Horarios guardados correctamente');
            $this->showForm = false;
            
            // Reload relationship
            $this->court->load('schedules');

        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Error saving schedules: ' . $e->getMessage());
            session()->flash('error', 'Error al guardar horarios: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.court-schedules');
    }
}
