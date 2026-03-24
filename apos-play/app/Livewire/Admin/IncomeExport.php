<?php

namespace App\Livewire\Admin;

use App\Models\Complex;
use App\Models\Court;
use App\Models\CourtsXAdmin;
use App\Models\Reservation;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class IncomeExport extends Component
{
    public string $filterMode = 'month'; // 'month' | 'range'
    public int    $selectedMonth;
    public int    $selectedYear;
    public string $dateFrom;
    public string $dateTo;

    public function mount(): void
    {
        $this->selectedMonth = (int) now()->format('n');
        $this->selectedYear  = (int) now()->format('Y');
        $this->dateFrom      = now()->startOfMonth()->format('Y-m-d');
        $this->dateTo        = now()->format('Y-m-d');
    }

    private function getDateRange(): array
    {
        if ($this->filterMode === 'month') {
            $start = Carbon::create($this->selectedYear, $this->selectedMonth, 1);
            return [$start->format('Y-m-d'), $start->copy()->endOfMonth()->format('Y-m-d')];
        }

        return [$this->dateFrom, $this->dateTo];
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

    private function getReservations()
    {
        [$from, $to] = $this->getDateRange();

        $query = Reservation::whereBetween('reservation_date', [$from, $to])
            ->where('amount_paid', '>', 0)
            ->with(['court', 'user'])
            ->orderBy('reservation_date')
            ->orderBy('start_time');

        $scopedCourtIds = $this->getScopedCourtIds();
        if ($scopedCourtIds !== null) {
            $query->whereIn('court_id', $scopedCourtIds);
        }

        return $query->get();
    }

    private function getTipo(Reservation $r): string
    {
        return match ($r->payment_status) {
            'refunded'         => 'Reembolso total',
            'partial_refunded' => 'Reembolso parcial',
            default            => 'Ingreso',
        };
    }

    public function exportCsv()
    {
        $reservations = $this->getReservations();

        if ($reservations->isEmpty()) {
            $this->dispatch('no-data');
            return;
        }

        [$from, $to] = $this->getDateRange();
        $filename = "ingresos_{$from}_{$to}.csv";

        return response()->streamDownload(function () use ($reservations) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF"); // BOM for Excel UTF-8

            fputcsv($handle, [
                'ID', 'Fecha', 'Cancha', 'Usuario', 'Email',
                'Precio Total', 'Descuento', 'Monto Pagado',
                'Estado', 'Estado de Pago', 'ID Pago MP', 'Tipo',
            ]);

            foreach ($reservations as $r) {
                fputcsv($handle, [
                    $r->id,
                    $r->reservation_date->format('d/m/Y'),
                    $r->court?->name ?? '-',
                    $r->user?->name  ?? '-',
                    $r->user?->email ?? '-',
                    number_format((float) $r->total_price, 2, '.', ''),
                    number_format((float) ($r->discount_amount ?? 0), 2, '.', ''),
                    number_format((float) $r->amount_paid, 2, '.', ''),
                    $r->status->value,
                    $r->payment_status ?? '-',
                    $r->payment_id     ?? '-',
                    $this->getTipo($r),
                ]);
            }

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function exportPdf()
    {
        $reservations = $this->getReservations();

        if ($reservations->isEmpty()) {
            $this->dispatch('no-data');
            return;
        }

        [$from, $to] = $this->getDateRange();

        $totalIncome  = $reservations
            ->whereNotIn('payment_status', ['refunded', 'partial_refunded'])
            ->sum('amount_paid');

        $totalRefunds = $reservations
            ->whereIn('payment_status', ['refunded', 'partial_refunded'])
            ->sum('amount_paid');

        $pdf = Pdf::loadView('pdf.income-report', [
            'reservations' => $reservations,
            'from'         => $from,
            'to'           => $to,
            'totalIncome'  => $totalIncome,
            'totalRefunds' => $totalRefunds,
            'getTipo'      => fn($r) => $this->getTipo($r),
        ])->setPaper('a4', 'landscape');

        $filename = "ingresos_{$from}_{$to}.pdf";

        return response()->streamDownload(
            fn() => print($pdf->output()),
            $filename,
            ['Content-Type' => 'application/pdf']
        );
    }

    public function render()
    {
        $reservations = $this->getReservations();

        $totalIncome  = $reservations
            ->whereNotIn('payment_status', ['refunded', 'partial_refunded'])
            ->sum('amount_paid');

        $totalRefunds = $reservations
            ->whereIn('payment_status', ['refunded', 'partial_refunded'])
            ->sum('amount_paid');

        return view('livewire.admin.income-export', [
            'totalRecords' => $reservations->count(),
            'totalIncome'  => $totalIncome,
            'totalRefunds' => $totalRefunds,
            'netIncome'    => $totalIncome - $totalRefunds,
        ]);
    }
}
