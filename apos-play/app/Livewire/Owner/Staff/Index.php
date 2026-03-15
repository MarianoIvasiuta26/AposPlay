<?php

namespace App\Livewire\Owner\Staff;

use App\Enums\UserRole;
use App\Models\User;
use App\Services\RoleService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Index extends Component
{
    public string $search = '';

    public function removeFromComplex(int $userId, int $complexId)
    {
        $staff = User::where('role', UserRole::STAFF)->findOrFail($userId);
        $complex = auth()->user()->isSuperadmin()
            ? \App\Models\Complex::findOrFail($complexId)
            : auth()->user()->complexesOwned()->findOrFail($complexId);

        app(RoleService::class)->removeStaffFromComplex($staff, $complex);
        session()->flash('success', 'Staff removido del complejo.');
    }

    public function render()
    {
        $user = auth()->user();

        $query = User::where('role', UserRole::STAFF)
            ->with('complexesStaff');

        if ($user->isOwner()) {
            $ownerComplexIds = $user->complexesOwned()->pluck('id');
            $query->whereHas('complexesStaff', function ($q) use ($ownerComplexIds) {
                $q->whereIn('complexes.id', $ownerComplexIds);
            });
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }

        $staffMembers = $query->orderByDesc('created_at')->get();

        return view('livewire.owner.staff.index', [
            'staffMembers' => $staffMembers,
        ]);
    }
}
