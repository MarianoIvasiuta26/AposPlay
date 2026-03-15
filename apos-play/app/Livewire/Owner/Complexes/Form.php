<?php

namespace App\Livewire\Owner\Complexes;

use App\Models\Complex;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Form extends Component
{
    public ?int $complexId = null;

    #[Rule('required|string|max:255')]
    public string $name = '';

    #[Rule('nullable|string|max:255')]
    public string $address = '';

    public function mount($complex = null)
    {
        if ($complex) {
            $complex = Complex::findOrFail($complex);
            $user = auth()->user();

            if (!$user->isSuperadmin() && $complex->owner_id !== $user->id) {
                abort(403);
            }

            $this->complexId = $complex->id;
            $this->name = $complex->name;
            $this->address = $complex->address ?? '';
        }
    }

    public function save()
    {
        $this->validate();

        DB::transaction(function () {
            if ($this->complexId) {
                $complex = Complex::findOrFail($this->complexId);
                $complex->update([
                    'name' => $this->name,
                    'address' => $this->address ?: null,
                ]);
            } else {
                Complex::create([
                    'name' => $this->name,
                    'owner_id' => auth()->id(),
                    'address' => $this->address ?: null,
                ]);
            }
        });

        session()->flash('success', $this->complexId ? 'Complejo actualizado.' : 'Complejo creado.');
        $this->redirect(route('owner.complexes'), navigate: true);
    }

    public function render()
    {
        return view('livewire.owner.complexes.form');
    }
}
