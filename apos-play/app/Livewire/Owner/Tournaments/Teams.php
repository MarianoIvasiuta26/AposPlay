<?php

namespace App\Livewire\Owner\Tournaments;

use App\Enums\TournamentStatus;
use App\Enums\TournamentTeamPaymentStatus;
use App\Models\Tournament;
use App\Models\TournamentTeam;
use App\Services\TournamentService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Teams extends Component
{
    public Tournament $tournament;
    public string $errorMessage = '';
    public string $successMessage = '';

    public function mount(Tournament $tournament): void
    {
        $this->tournament = $tournament;
        $this->checkOwnership($tournament);
    }

    public function markAsPaid(int $teamId): void
    {
        $this->errorMessage = '';

        $team = TournamentTeam::where('id', $teamId)
            ->where('tournament_id', $this->tournament->id)
            ->firstOrFail();

        try {
            app(TournamentService::class)->markTeamPaid($team, 'manual-' . now()->timestamp);
            $this->successMessage = "Equipo {$team->name} marcado como pagado.";
        } catch (\Exception $e) {
            $this->errorMessage = $e->getMessage();
        }
    }

    public function withdrawTeam(int $teamId): void
    {
        $this->errorMessage = '';

        $team = TournamentTeam::where('id', $teamId)
            ->where('tournament_id', $this->tournament->id)
            ->firstOrFail();

        try {
            app(TournamentService::class)->withdrawTeam($team);
            $this->successMessage = "Equipo {$team->name} dado de baja del torneo.";
        } catch (\Exception $e) {
            $this->errorMessage = $e->getMessage();
        }
    }

    public function openRegistration(): void
    {
        $this->errorMessage = '';
        try {
            app(TournamentService::class)->openRegistration($this->tournament);
            $this->tournament->refresh();
            $this->successMessage = 'Inscripciones abiertas.';
        } catch (\Exception $e) {
            $this->errorMessage = $e->getMessage();
        }
    }

    public function startTournament(): void
    {
        $this->errorMessage = '';
        try {
            app(TournamentService::class)->startTournament($this->tournament);
            $this->tournament->refresh();
            $this->successMessage = 'Torneo iniciado. Ya podés generar el fixture.';
        } catch (\Exception $e) {
            $this->errorMessage = $e->getMessage();
        }
    }

    public function finishTournament(): void
    {
        $this->errorMessage = '';
        try {
            app(TournamentService::class)->finishTournament($this->tournament);
            $this->tournament->refresh();
            $this->successMessage = 'Torneo finalizado.';
        } catch (\Exception $e) {
            $this->errorMessage = $e->getMessage();
        }
    }

    private function checkOwnership(Tournament $tournament): void
    {
        if ($tournament->owner_id !== auth()->id() && !auth()->user()->isSuperadmin()) {
            abort(403);
        }
    }

    public function render()
    {
        $teams = $this->tournament->teams()
            ->with(['captain', 'members.user'])
            ->withCount('members')
            ->orderBy('name')
            ->get();

        $paidTeamsCount = $this->tournament->teams()
            ->where('payment_status', TournamentTeamPaymentStatus::PAID->value)
            ->count();

        $totalCollected = $this->tournament->teams()
            ->where('payment_status', TournamentTeamPaymentStatus::PAID->value)
            ->sum('amount_paid');

        return view('livewire.owner.tournaments.teams', [
            'teams'          => $teams,
            'paidTeamsCount' => $paidTeamsCount,
            'totalCollected' => $totalCollected,
        ])->title("Equipos - {$this->tournament->name}");
    }
}
