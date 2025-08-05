<?php
namespace App\Livewire\User;

use Livewire\Component;
use App\Models\Court;
use App\Models\Schedule;
use App\Models\Reservation;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class ReservationForm extends Component
{
    public Court $court;
    public Schedule $schedule;
    public string $date;

    public string $start_time = '';
    public int $duration_hours = 1;

    public function mount(Court $court, Schedule $schedule, $date)
    {
        $this->court = $court;
        $this->schedule = $schedule;
        $this->date = $date;
    }

    public function submit()
    {
        $this->validate([
            'start_time' => 'required|date_format:H:i',
            'duration_hours' => 'required|integer|min:1|max:4',
        ]);

        // Verificar si la hora está disponible (pendiente de implementar)
            // Calcular el precio total
            $total = $this->schedule->price * $this->duration_hours;
        Reservation::create([
            'user_id' => Auth::id(),
            'court_id' => $this->court->id,
            'schedule_id' => $this->schedule->id,
            'reservation_date' => $this->date,
            'start_time' => $this->start_time,
            'duration_hours' => $this->duration_hours,
            'status' => 'pending',
            'total_price' => $total,
        ]);

        session()->flash('success', 'Reserva creada correctamente.');

        return redirect()->route('reservations.create', [
            'court' => $this->court->id,
            'schedule' => $this->schedule->id,
            'date' => $this->date,
        ]);
    }

    public function render()
    {
        return view('livewire.user.reservation-form');
    }
}
