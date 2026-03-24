<?php

namespace App\Livewire\Tournaments;

use App\Models\Tournament;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Index extends Component
{
    public string $filterSport = '';

    public function render()
    {
        $query = Tournament::active()
            ->with(['teams', 'court', 'owner'])
            ->withCount('teams');

        if ($this->filterSport !== '') {
            $query->where('sport_type', $this->filterSport);
        }

        $tournaments = $query->orderByDesc('created_at')->get();

        $sports = Tournament::active()
            ->distinct()
            ->pluck('sport_type')
            ->filter()
            ->values();

        return view('livewire.tournaments.index', [
            'tournaments' => $tournaments,
            'sports'      => $sports,
        ])->title('Torneos');
    }
}
