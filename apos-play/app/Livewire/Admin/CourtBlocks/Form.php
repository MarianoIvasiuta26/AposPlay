<?php

namespace App\Livewire\Admin\CourtBlocks;

use App\Models\Court;
use App\Models\CourtBlock;
use App\Services\CourtBlockService;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Form extends Component
{
    #[Rule('required|exists:courts,id')]
    public $court_id = '';

    #[Rule('required|date|after_or_equal:today')]
    public $start_date = '';

    #[Rule('required|date|after_or_equal:start_date')]
    public $end_date = '';

    #[Rule('nullable|date_format:H:i')]
    public $start_time = '';

    #[Rule('nullable|date_format:H:i|after:start_time')]
    public $end_time = '';

    #[Rule('required|string|max:255')]
    public string $reason = '';

    public bool $fullDay = true;

    public function save()
    {
        Gate::authorize('create', CourtBlock::class);

        $this->validate();

        $data = [
            'court_id' => $this->court_id,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'start_time' => $this->fullDay ? null : ($this->start_time ?: null),
            'end_time' => $this->fullDay ? null : ($this->end_time ?: null),
            'reason' => $this->reason,
            'created_by' => auth()->id(),
        ];

        app(CourtBlockService::class)->createBlock($data);

        session()->flash('success', 'Bloqueo creado exitosamente.');
        return $this->redirect(route('admin.court-blocks'), navigate: true);
    }

    public function render()
    {
        $user = auth()->user();

        if ($user->isSuperadmin()) {
            $courts = Court::all();
        } else {
            $complexIds = $user->complexesOwned()->pluck('id');
            $courts = Court::whereIn('complex_id', $complexIds)->get();
        }

        return view('livewire.admin.court-blocks.form', [
            'courts' => $courts,
        ]);
    }
}
