<?php

namespace App\Livewire\Admin\CourtBlocks;

use App\Models\Court;
use App\Models\CourtBlock;
use App\Services\CourtBlockService;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Index extends Component
{
    public function deleteBlock(int $blockId)
    {
        $block = CourtBlock::findOrFail($blockId);
        Gate::authorize('delete', $block);

        app(CourtBlockService::class)->deleteBlock($block);
        session()->flash('success', 'Bloqueo eliminado exitosamente.');
    }

    public function render()
    {
        $user = auth()->user();

        $query = CourtBlock::with(['court', 'creator'])->active();

        if ($user->isOwner()) {
            $complexIds = $user->complexesOwned()->pluck('id');
            $courtIds = Court::whereIn('complex_id', $complexIds)->pluck('id');
            $query->whereIn('court_id', $courtIds);
        }

        $blocks = $query->orderByDesc('start_date')->get();

        return view('livewire.admin.court-blocks.index', [
            'blocks' => $blocks,
        ]);
    }
}
