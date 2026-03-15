<?php

namespace App\Livewire\Owner\Staff;

use App\Models\Complex;
use App\Services\RoleService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Form extends Component
{
    #[Rule('required|string|max:255')]
    public string $name = '';

    #[Rule('required|email|unique:users,email')]
    public string $email = '';

    #[Rule('required|string|min:8')]
    public string $password = '';

    #[Rule('required|array|min:1')]
    public array $selectedComplexes = [];

    public function save()
    {
        $this->validate();

        $user = auth()->user();

        // Verify owner actually owns the selected complexes
        if ($user->isOwner()) {
            $ownedIds = $user->complexesOwned()->pluck('id')->toArray();
            foreach ($this->selectedComplexes as $id) {
                if (!in_array($id, $ownedIds)) {
                    abort(403);
                }
            }
        }

        app(RoleService::class)->createStaff([
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
        ], $this->selectedComplexes);

        session()->flash('success', 'Staff creado exitosamente.');
        $this->redirect(route('owner.staff'), navigate: true);
    }

    public function render()
    {
        $user = auth()->user();

        $complexes = $user->isSuperadmin()
            ? Complex::active()->get()
            : $user->complexesOwned()->active()->get();

        return view('livewire.owner.staff.form', [
            'complexes' => $complexes,
        ]);
    }
}
