<?php

namespace App\Livewire\Admin\Owners;

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

    #[Rule('nullable|string|max:255')]
    public string $complexName = '';

    #[Rule('nullable|string|max:255')]
    public string $complexAddress = '';

    public function save()
    {
        $this->validate();

        app(RoleService::class)->createOwner([
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
            'complex_name' => $this->complexName,
            'complex_address' => $this->complexAddress,
        ]);

        session()->flash('success', 'Owner creado exitosamente.');
        $this->redirect(route('admin.owners'), navigate: true);
    }

    public function render()
    {
        return view('livewire.admin.owners.form');
    }
}
