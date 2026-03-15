<?php

namespace App\Livewire\Admin\Owners;

use App\Enums\UserRole;
use App\Models\User;
use App\Services\RoleService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Index extends Component
{
    public string $search = '';

    public function deactivate(int $userId)
    {
        $owner = User::where('role', UserRole::OWNER)->findOrFail($userId);
        app(RoleService::class)->deactivateOwner($owner);
        session()->flash('success', 'Owner desactivado exitosamente.');
    }

    public function reactivate(int $userId)
    {
        $owner = User::where('role', UserRole::OWNER)->findOrFail($userId);
        app(RoleService::class)->reactivateOwner($owner);
        session()->flash('success', 'Owner reactivado exitosamente.');
    }

    public function render()
    {
        $owners = User::where('role', UserRole::OWNER)
            ->with('complexesOwned')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%');
                });
            })
            ->orderByDesc('created_at')
            ->get();

        return view('livewire.admin.owners.index', [
            'owners' => $owners,
        ]);
    }
}
