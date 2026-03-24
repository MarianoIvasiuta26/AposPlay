<?php

namespace App\Livewire\Tournaments;

use App\Models\Tournament;
use App\Models\TournamentTeam;
use App\Models\TournamentTeamMember;
use App\Models\User;
use App\Services\TournamentService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Register extends Component
{
    public Tournament $tournament;

    public int $step = 1;

    #[Rule('required|min:2|max:100')]
    public string $teamName = '';

    public string $searchEmail = '';
    public array $searchResults = [];

    public ?TournamentTeam $team = null;
    public string $errorMessage = '';
    public string $successMessage = '';

    public function mount(Tournament $tournament): void
    {
        $this->tournament = $tournament;

        if (!$tournament->isRegistrationOpen()) {
            session()->flash('error', 'Las inscripciones para este torneo no están abiertas.');
        }

        // Check if user already has a team
        $member = TournamentTeamMember::whereHas('team', function ($q) use ($tournament) {
            $q->where('tournament_id', $tournament->id)->whereNull('deleted_at');
        })->where('user_id', auth()->id())->whereNull('deleted_at')->first();

        if ($member) {
            $this->team = $member->team()->with('members.user', 'tournament')->first();
            $this->step = 2;
        }
    }

    public function createTeam(): void
    {
        $this->validateOnly('teamName');
        $this->errorMessage = '';

        try {
            $service = app(TournamentService::class);
            $this->team = $service->registerTeam($this->tournament, $this->teamName, auth()->user());
            $this->team->load('members.user', 'tournament');
            $this->step = 2;
            $this->successMessage = 'Equipo creado exitosamente. Ahora puedes agregar miembros.';
        } catch (\Exception $e) {
            $this->errorMessage = $e->getMessage();
        }
    }

    public function searchUsers(): void
    {
        if (strlen($this->searchEmail) < 3) {
            $this->searchResults = [];
            return;
        }

        $alreadyInTeamUserIds = TournamentTeamMember::whereHas('team', function ($q) {
            $q->where('tournament_id', $this->tournament->id)->whereNull('deleted_at');
        })->whereNull('deleted_at')->pluck('user_id')->toArray();

        $this->searchResults = User::where('email', 'like', '%' . $this->searchEmail . '%')
            ->whereNotIn('id', $alreadyInTeamUserIds)
            ->limit(5)
            ->get(['id', 'name', 'email'])
            ->toArray();
    }

    public function addMember(int $userId): void
    {
        $this->errorMessage = '';

        if (!$this->team) {
            $this->errorMessage = 'Primero debes crear tu equipo.';
            return;
        }

        try {
            $user = User::findOrFail($userId);
            $service = app(TournamentService::class);
            $service->addMember($this->team, $user);
            $this->team->load('members.user');
            $this->searchEmail = '';
            $this->searchResults = [];
            $this->successMessage = "Jugador {$user->name} agregado al equipo.";
        } catch (\Exception $e) {
            $this->errorMessage = $e->getMessage();
        }
    }

    public function removeMember(int $userId): void
    {
        $this->errorMessage = '';

        if (!$this->team) {
            return;
        }

        try {
            $user = User::findOrFail($userId);
            $service = app(TournamentService::class);
            $service->removeMember($this->team, $user);
            $this->team->load('members.user');
            $this->successMessage = "Jugador removido del equipo.";
        } catch (\Exception $e) {
            $this->errorMessage = $e->getMessage();
        }
    }

    public function goToPayment(): void
    {
        $this->step = 3;
    }

    public function pay(): void
    {
        $this->errorMessage = '';

        if (!$this->team) {
            $this->errorMessage = 'No hay equipo registrado.';
            return;
        }

        try {
            $service = app(TournamentService::class);
            $url = $service->createPaymentPreference($this->team);
            $this->redirect($url);
        } catch (\Exception $e) {
            $this->errorMessage = $e->getMessage();
        }
    }

    public function render()
    {
        return view('livewire.tournaments.register')->title('Inscribirse - ' . $this->tournament->name);
    }
}
