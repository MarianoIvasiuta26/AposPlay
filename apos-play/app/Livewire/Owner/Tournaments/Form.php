<?php

namespace App\Livewire\Owner\Tournaments;

use App\Enums\TournamentStatus;
use App\Models\Court;
use App\Models\Complex;
use App\Models\Tournament;
use App\Services\TournamentService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Form extends Component
{
    public ?int $tournamentId = null;

    #[Rule('required|min:2|max:255')]
    public string $name = '';

    #[Rule('nullable|max:2000')]
    public string $description = '';

    #[Rule('required|in:futbol,padel,tenis,basquet,voley')]
    public string $sport_type = 'futbol';

    #[Rule('required|in:round_robin,single_elimination')]
    public string $format = 'round_robin';

    #[Rule('nullable|exists:courts,id')]
    public ?int $court_id = null;

    #[Rule('required|integer|min:2|max:64')]
    public int $max_teams = 8;

    #[Rule('required|integer|min:2|max:64')]
    public int $min_teams = 4;

    #[Rule('required|integer|min:1|max:30')]
    public int $min_players = 1;

    #[Rule('required|integer|min:1|max:30')]
    public int $max_players = 10;

    #[Rule('required|numeric|min:0')]
    public string $entry_fee = '0';

    #[Rule('nullable|max:2000')]
    public string $prize_description = '';

    #[Rule('required|date|after:now')]
    public string $registration_deadline = '';

    #[Rule('required|date')]
    public string $starts_at = '';

    #[Rule('nullable|date|after:starts_at')]
    public string $ends_at = '';

    public string $errorMessage = '';
    public string $successMessage = '';

    public function mount(): void
    {
        // Load tournament data when editing (route has {tournament} parameter)
        $tournamentId = request()->route('tournament');
        if ($tournamentId) {
            $tournament = Tournament::findOrFail($tournamentId);
            $this->tournamentId          = $tournament->id;
            $this->name                  = $tournament->name;
            $this->description           = $tournament->description ?? '';
            $this->sport_type            = $tournament->sport_type;
            $this->format                = $tournament->format->value;
            $this->court_id              = $tournament->court_id;
            $this->max_teams             = $tournament->max_teams;
            $this->min_teams             = $tournament->min_teams ?? 2;
            $this->min_players           = $tournament->min_players;
            $this->max_players           = $tournament->max_players;
            $this->entry_fee             = (string) $tournament->entry_fee;
            $this->prize_description     = $tournament->prize_description ?? '';
            $this->registration_deadline = $tournament->registration_deadline?->format('Y-m-d\TH:i') ?? '';
            $this->starts_at             = $tournament->starts_at?->format('Y-m-d') ?? '';
            $this->ends_at               = $tournament->ends_at?->format('Y-m-d') ?? '';
        }
    }

    public function save(string $action = 'draft'): void
    {
        $this->validate();
        $this->errorMessage = '';

        try {
            $data = [
                'name'                  => $this->name,
                'description'           => $this->description ?: null,
                'sport_type'            => $this->sport_type,
                'format'                => $this->format,
                'court_id'              => $this->court_id,
                'max_teams'             => $this->max_teams,
                'min_teams'             => $this->min_teams,
                'min_players'           => $this->min_players,
                'max_players'           => $this->max_players,
                'entry_fee'             => $this->entry_fee,
                'prize_description'     => $this->prize_description ?: null,
                'registration_deadline' => $this->registration_deadline,
                'starts_at'             => $this->starts_at,
                'ends_at'               => $this->ends_at ?: null,
                'status'                => TournamentStatus::DRAFT->value,
            ];

            $service = app(TournamentService::class);

            if ($this->tournamentId) {
                $tournament = Tournament::findOrFail($this->tournamentId);
                $tournament->update($data);
            } else {
                $tournament = $service->create($data, auth()->user());
            }

            if ($action === 'open') {
                $service->openRegistration($tournament);
            }

            $this->redirect(route('owner.tournaments.index'));
        } catch (\Exception $e) {
            $this->errorMessage = $e->getMessage();
        }
    }

    public function render()
    {
        $courts = collect();

        $user = auth()->user();
        if ($user->isSuperadmin()) {
            $courts = Court::withoutTrashed()->get(['id', 'name', 'type']);
        } else {
            $complexIds = Complex::where('owner_id', $user->id)->pluck('id');
            if ($complexIds->isNotEmpty()) {
                $courts = Court::whereIn('complex_id', $complexIds)->withoutTrashed()->get(['id', 'name', 'type']);
            }
        }

        return view('livewire.owner.tournaments.form', [
            'courts' => $courts,
        ]);
    }
}
