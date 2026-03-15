<?php

namespace App\Livewire\Owner\Complexes;

use App\Models\Complex;
use App\Services\RoleService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Index extends Component
{
    public function toggleActive(int $complexId)
    {
        $user = auth()->user();

        $complex = $user->isSuperadmin()
            ? Complex::findOrFail($complexId)
            : $user->complexesOwned()->findOrFail($complexId);

        $complex->update(['active' => !$complex->active]);
    }

    public function render()
    {
        $complexes = app(RoleService::class)->getComplexesForUser(auth()->user());
        $complexes->load(['owner', 'staff', 'courts']);

        return view('livewire.owner.complexes.index', [
            'complexes' => $complexes,
        ]);
    }
}
