<?php

namespace App\Livewire\Owner\Tournaments;

use App\Enums\TournamentMatchStatus;
use App\Models\Tournament;
use App\Models\TournamentMatch;
use App\Models\TournamentTeamMember;
use App\Services\TournamentService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Fixture extends Component
{
    public Tournament $tournament;

    // Result modal
    public bool $showResultModal = false;
    public ?int $editingMatchId = null;
    public int $homeScore = 0;
    public int $awayScore = 0;

    // Player stats: array of ['user_id', 'team_id', 'goals', 'assists', 'yellow_cards', 'red_cards']
    public array $playerStats = [];

    public string $errorMessage = '';
    public string $successMessage = '';

    public function mount(Tournament $tournament): void
    {
        $this->tournament = $tournament;

        if ($tournament->owner_id !== auth()->id() && !auth()->user()->isSuperadmin()) {
            abort(403);
        }
    }

    public function generateFixture(): void
    {
        $this->errorMessage = '';

        try {
            app(TournamentService::class)->generateFixture($this->tournament);
            $this->successMessage = 'Fixture generado exitosamente.';
        } catch (\Exception $e) {
            $this->errorMessage = $e->getMessage();
        }
    }

    public function openResultModal(int $matchId): void
    {
        $match = TournamentMatch::with(['homeTeam.members.user', 'awayTeam.members.user'])
            ->where('tournament_id', $this->tournament->id)
            ->findOrFail($matchId);

        $this->editingMatchId = $matchId;
        $this->homeScore = $match->home_score ?? 0;
        $this->awayScore = $match->away_score ?? 0;

        // Build player stats array from existing or initialize
        $this->playerStats = [];
        $existingStats = $match->playerStats()->get()->keyBy(function ($s) {
            return $s->user_id . '_' . $s->team_id;
        });

        foreach ([$match->homeTeam, $match->awayTeam] as $team) {
            if (!$team) continue;
            foreach ($team->members as $member) {
                $key = $member->user_id . '_' . $team->id;
                $existing = $existingStats->get($key);
                $this->playerStats[] = [
                    'user_id'      => $member->user_id,
                    'team_id'      => $team->id,
                    'user_name'    => $member->user->name ?? 'Jugador',
                    'team_name'    => $team->name,
                    'goals'        => $existing ? $existing->goals : 0,
                    'assists'      => $existing ? $existing->assists : 0,
                    'yellow_cards' => $existing ? $existing->yellow_cards : 0,
                    'red_cards'    => $existing ? $existing->red_cards : 0,
                ];
            }
        }

        $this->showResultModal = true;
    }

    public function closeResultModal(): void
    {
        $this->showResultModal = false;
        $this->reset(['editingMatchId', 'homeScore', 'awayScore', 'playerStats']);
    }

    public function saveResult(): void
    {
        $this->errorMessage = '';

        if (!$this->editingMatchId) {
            return;
        }

        $match = TournamentMatch::where('tournament_id', $this->tournament->id)
            ->findOrFail($this->editingMatchId);

        try {
            app(TournamentService::class)->recordResult(
                $match,
                $this->homeScore,
                $this->awayScore,
                $this->playerStats
            );

            $this->successMessage = 'Resultado registrado exitosamente.';
            $this->closeResultModal();
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
            $this->successMessage = 'El torneo ha finalizado.';
        } catch (\Exception $e) {
            $this->errorMessage = $e->getMessage();
        }
    }

    public function render()
    {
        $this->tournament->load(['teams', 'matches.homeTeam', 'matches.awayTeam']);

        $matchesByRound = $this->tournament->matches
            ->sortBy('round')
            ->groupBy('round');

        $editingMatch = null;
        if ($this->editingMatchId) {
            $editingMatch = TournamentMatch::with(['homeTeam', 'awayTeam'])->find($this->editingMatchId);
        }

        return view('livewire.owner.tournaments.fixture', [
            'matchesByRound' => $matchesByRound,
            'editingMatch'   => $editingMatch,
        ])->title("Fixture - {$this->tournament->name}");
    }
}
