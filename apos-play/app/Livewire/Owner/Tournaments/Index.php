<?php

namespace App\Livewire\Owner\Tournaments;

use App\Enums\TournamentStatus;
use App\Models\Tournament;
use App\Services\TournamentService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Index extends Component
{
    public string $errorMessage = '';
    public string $successMessage = '';

    public function openRegistration(int $tournamentId): void
    {
        $this->errorMessage = '';
        $tournament = $this->getOwnedTournament($tournamentId);

        try {
            app(TournamentService::class)->openRegistration($tournament);
            $this->successMessage = 'Las inscripciones han sido abiertas.';
        } catch (\Exception $e) {
            $this->errorMessage = $e->getMessage();
        }
    }

    public function startTournament(int $tournamentId): void
    {
        $this->errorMessage = '';
        $tournament = $this->getOwnedTournament($tournamentId);

        try {
            app(TournamentService::class)->startTournament($tournament);
            $this->successMessage = 'El torneo ha comenzado.';
        } catch (\Exception $e) {
            $this->errorMessage = $e->getMessage();
        }
    }

    public function finishTournament(int $tournamentId): void
    {
        $this->errorMessage = '';
        $tournament = $this->getOwnedTournament($tournamentId);

        try {
            app(TournamentService::class)->finishTournament($tournament);
            $this->successMessage = 'El torneo ha finalizado.';
        } catch (\Exception $e) {
            $this->errorMessage = $e->getMessage();
        }
    }

    public function deleteTournament(int $tournamentId): void
    {
        $tournament = $this->getOwnedTournament($tournamentId);

        if (!in_array($tournament->status->value, [TournamentStatus::DRAFT->value, TournamentStatus::CANCELLED->value])) {
            $this->errorMessage = 'Solo se pueden eliminar torneos en borrador o cancelados.';
            return;
        }

        $tournament->delete();
        $this->successMessage = 'Torneo eliminado.';
    }

    private function getOwnedTournament(int $id): Tournament
    {
        return Tournament::where('id', $id)
            ->where('owner_id', auth()->id())
            ->firstOrFail();
    }

    public function render()
    {
        $tournaments = Tournament::where('owner_id', auth()->id())
            ->withCount('teams')
            ->orderByDesc('created_at')
            ->get();

        return view('livewire.owner.tournaments.index', [
            'tournaments' => $tournaments,
        ])->title('Mis Torneos');
    }
}
