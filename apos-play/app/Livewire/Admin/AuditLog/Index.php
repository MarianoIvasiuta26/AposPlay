<?php

namespace App\Livewire\Admin\AuditLog;

use App\Enums\AuditAction;
use App\Enums\UserRole;
use App\Models\AuditLog;
use App\Models\User;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
class Index extends Component
{
    use WithPagination;

    public string $filterUser = '';
    public string $filterAction = '';
    public string $filterModel = '';
    public string $filterDateFrom = '';
    public string $filterDateTo = '';

    public function updatingFilterUser(): void
    {
        $this->resetPage();
    }

    public function updatingFilterAction(): void
    {
        $this->resetPage();
    }

    public function updatingFilterModel(): void
    {
        $this->resetPage();
    }

    public function updatingFilterDateFrom(): void
    {
        $this->resetPage();
    }

    public function updatingFilterDateTo(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->reset(['filterUser', 'filterAction', 'filterModel', 'filterDateFrom', 'filterDateTo']);
        $this->resetPage();
    }

    public function exportPdf()
    {
        $query = $this->buildQuery();
        $logs = $query->limit(1000)->get();

        $filters = $this->getActiveFilters();
        $user = auth()->user();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('livewire.admin.audit-log.pdf', [
            'logs' => $logs,
            'filters' => $filters,
            'generatedAt' => now()->timezone('America/Argentina/Buenos_Aires')->format('d/m/Y H:i'),
            'generatedBy' => $user->name,
        ])->setPaper('a4', 'landscape');

        return response()->streamDownload(
            fn () => print($pdf->output()),
            'auditoria_' . now()->format('Y-m-d_His') . '.pdf'
        );
    }

    public function render()
    {
        $query = $this->buildQuery();
        $logs = $query->paginate(25);

        return view('livewire.admin.audit-log.index', [
            'logs' => $logs,
            'users' => $this->getAvailableUsers(),
            'actions' => AuditAction::cases(),
            'modelMap' => AuditLog::auditableModelMap(),
        ]);
    }

    private function buildQuery()
    {
        $user = auth()->user();

        $query = AuditLog::with('user')
            ->orderByDesc('created_at');

        // Owner scope: only their complexes/courts
        if ($user->isOwner()) {
            $query->forOwner($user);
        }

        if ($this->filterUser !== '') {
            $query->where('user_id', $this->filterUser);
        }

        if ($this->filterAction !== '') {
            $query->where('action', $this->filterAction);
        }

        if ($this->filterModel !== '') {
            $query->where('auditable_type', $this->filterModel);
        }

        if ($this->filterDateFrom !== '') {
            $query->whereDate('created_at', '>=', $this->filterDateFrom);
        }

        if ($this->filterDateTo !== '') {
            $query->whereDate('created_at', '<=', $this->filterDateTo);
        }

        return $query;
    }

    private function getAvailableUsers()
    {
        $user = auth()->user();

        if ($user->isSuperadmin()) {
            return User::orderBy('name')->get(['id', 'name', 'email']);
        }

        // Owner: only users that interacted with their complexes
        $complexIds = $user->complexesOwned()->pluck('id');
        $staffIds = \DB::table('complex_staff')
            ->whereIn('complex_id', $complexIds)
            ->pluck('user_id');

        $courtIds = \App\Models\Court::whereIn('complex_id', $complexIds)->pluck('id');
        $reservationUserIds = \App\Models\Reservation::whereIn('court_id', $courtIds)->pluck('user_id');

        $userIds = $staffIds->merge($reservationUserIds)->merge([$user->id])->unique();

        return User::whereIn('id', $userIds)->orderBy('name')->get(['id', 'name', 'email']);
    }

    private function getActiveFilters(): array
    {
        $filters = [];

        if ($this->filterUser !== '') {
            $user = User::find($this->filterUser);
            $filters['Usuario'] = $user ? $user->name : 'Desconocido';
        }
        if ($this->filterAction !== '') {
            $action = AuditAction::tryFrom($this->filterAction);
            $filters['Acción'] = $action ? $action->label() : $this->filterAction;
        }
        if ($this->filterModel !== '') {
            $modelMap = array_flip(AuditLog::auditableModelMap());
            $filters['Modelo'] = $modelMap[$this->filterModel] ?? class_basename($this->filterModel);
        }
        if ($this->filterDateFrom !== '') {
            $filters['Desde'] = $this->filterDateFrom;
        }
        if ($this->filterDateTo !== '') {
            $filters['Hasta'] = $this->filterDateTo;
        }

        return $filters;
    }
}
