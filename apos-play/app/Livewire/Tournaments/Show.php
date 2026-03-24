<?php

namespace App\Livewire\Tournaments;

use App\Enums\TournamentFormat;
use App\Enums\TournamentStatus;
use App\Models\Tournament;
use App\Models\TournamentTeam;
use App\Services\TournamentService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Show extends Component
{
    public Tournament $tournament;
    public string $activeTab = 'fixture';
    public string $errorMessage = '';
    public string $successMessage = '';

    public function mount(Tournament $tournament): void
    {
        $this->tournament = $tournament;
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function withdrawMyTeam(): void
    {
        $this->errorMessage = '';
        $user = auth()->user();

        $team = TournamentTeam::where('tournament_id', $this->tournament->id)
            ->where('captain_id', $user->id)
            ->whereNull('deleted_at')
            ->first();

        if (!$team) {
            $this->errorMessage = 'No se encontró tu equipo en este torneo.';
            return;
        }

        try {
            app(TournamentService::class)->withdrawTeam($team);
            $this->tournament->refresh();
            $this->successMessage = 'Tu equipo fue dado de baja del torneo.';
        } catch (\Exception $e) {
            $this->errorMessage = $e->getMessage();
        }
    }

    public function render()
    {
        $service = app(TournamentService::class);

        $this->tournament->load(['teams.members.user', 'matches.homeTeam', 'matches.awayTeam', 'court', 'owner']);

        $matchesByRound = $this->tournament->matches
            ->sortBy('round')
            ->groupBy('round');

        $standings = null;
        if ($this->tournament->format === TournamentFormat::ROUND_ROBIN) {
            $standings = $service->getStandings($this->tournament);
        }

        $playerStats = $service->getPlayerStats($this->tournament);

        return view('livewire.tournaments.show', [
            'matchesByRound' => $matchesByRound,
            'standings'      => $standings,
            'playerStats'    => $playerStats,
        ])->title($this->tournament->name);
    }
}
