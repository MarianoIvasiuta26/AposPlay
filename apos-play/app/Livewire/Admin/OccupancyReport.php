<?php

namespace App\Livewire\Admin;

use App\Enums\ReservationStatus;
use App\Models\Complex;
use App\Models\Court;
use App\Models\CourtsXAdmin;
use App\Models\Reservation;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class OccupancyReport extends Component
{
    public string $courtFilter = 'all';
    public string $dateFrom;
    public string $dateTo;
    public string $reportType = 'cancha';

    private const DAYS = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
    private const MONTHS = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];

    public function mount(): void
    {
        $this->dateFrom = now()->startOfMonth()->format('Y-m-d');
        $this->dateTo = now()->format('Y-m-d');
    }

    public function setPreset(string $preset): void
    {
        [$this->dateFrom, $this->dateTo] = match ($preset) {
            'today' => [now()->format('Y-m-d'), now()->format('Y-m-d')],
            'week'  => [now()->startOfWeek()->format('Y-m-d'), now()->endOfWeek()->format('Y-m-d')],
            'month' => [now()->startOfMonth()->format('Y-m-d'), now()->endOfMonth()->format('Y-m-d')],
            default => [$this->dateFrom, $this->dateTo],
        };
    }

    private function getScopedCourtIds(): ?array
    {
        $user = auth()->user();

        if ($user->isSuperadmin()) {
            return null;
        }

        $complexIds = Complex::where('owner_id', $user->id)->pluck('id');
        $courtIdsFromComplexes = Court::whereIn('complex_id', $complexIds)->pluck('id');
        $courtIdsFromAdmin = CourtsXAdmin::where('user_id', $user->id)->pluck('court_id');

        return $courtIdsFromComplexes->merge($courtIdsFromAdmin)->unique()->values()->all();
    }

    public function render()
    {
        $scopedCourtIds = $this->getScopedCourtIds();

        $courtsQuery = Court::orderBy('name');
        if ($scopedCourtIds !== null) {
            $courtsQuery->whereIn('id', $scopedCourtIds);
        }
        $courts = $courtsQuery->get();

        $query = Reservation::whereBetween('reservation_date', [$this->dateFrom, $this->dateTo])
            ->where('status', '!=', ReservationStatus::CANCELLED->value)
            ->with('court');

        if ($scopedCourtIds !== null) {
            $query->whereIn('court_id', $scopedCourtIds);
        }

        if ($this->courtFilter !== 'all') {
            $query->where('court_id', $this->courtFilter);
        }

        $reservations = $query->get();

        $totalReservations = $reservations->count();
        $totalIncome = $reservations->sum('amount_paid');

        $breakdown = match ($this->reportType) {
            'cancha' => $reservations
                ->groupBy('court_id')
                ->map(fn($group) => [
                    'label'  => $group->first()->court?->name ?? 'Sin cancha',
                    'count'  => $group->count(),
                    'income' => $group->sum('amount_paid'),
                ])
                ->sortByDesc('count')
                ->values(),

            'horario' => $reservations
                ->groupBy(fn($r) => Carbon::parse($r->start_time)->format('H:i'))
                ->map(fn($group, $slot) => [
                    'label'  => $slot,
                    'count'  => $group->count(),
                    'income' => $group->sum('amount_paid'),
                ])
                ->sortBy('label')
                ->values(),

            'dia' => $reservations
                ->groupBy(fn($r) => Carbon::parse($r->reservation_date)->dayOfWeek)
                ->map(fn($group, $dayNum) => [
                    'label'  => self::DAYS[$dayNum],
                    'count'  => $group->count(),
                    'income' => $group->sum('amount_paid'),
                ])
                ->sortBy(fn($item, $key) => $key)
                ->values(),

            'semana' => $reservations
                ->groupBy(fn($r) => Carbon::parse($r->reservation_date)->format('Y-W'))
                ->map(fn($group, $yearWeek) => [
                    'label'  => 'Semana ' . substr($yearWeek, 5) . ' / ' . substr($yearWeek, 0, 4),
                    'count'  => $group->count(),
                    'income' => $group->sum('amount_paid'),
                ])
                ->sortBy(fn($item, $key) => $key)
                ->values(),

            'mes' => $reservations
                ->groupBy(fn($r) => Carbon::parse($r->reservation_date)->format('Y-m'))
                ->map(fn($group, $yearMonth) => [
                    'label'  => self::MONTHS[(int) substr($yearMonth, 5) - 1] . ' ' . substr($yearMonth, 0, 4),
                    'count'  => $group->count(),
                    'income' => $group->sum('amount_paid'),
                ])
                ->sortBy(fn($item, $key) => $key)
                ->values(),

            default => collect(),
        };

        $maxCount = $breakdown->max('count') ?: 1;

        return view('livewire.admin.occupancy-report', [
            'courts'            => $courts,
            'totalReservations' => $totalReservations,
            'totalIncome'       => $totalIncome,
            'breakdown'         => $breakdown,
            'maxCount'          => $maxCount,
        ]);
    }
}
