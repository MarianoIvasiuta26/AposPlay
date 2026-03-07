<?php

namespace App\Livewire\Admin\Promotions;

use App\Models\Promotion;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class Index extends Component
{
    public string $search = '';

    public function toggleStatus(int $promotionId)
    {
        $promotion = Promotion::findOrFail($promotionId);
        $promotion->update(['is_active' => !$promotion->is_active]);
    }

    public function deletePromotion(int $promotionId)
    {
        $promotion = Promotion::findOrFail($promotionId);
        $promotion->delete();
        session()->flash('success', 'Promoción eliminada exitosamente.');
    }

    public function render()
    {
        $promotions = Promotion::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->orderByDesc('created_at')
            ->get();

        return view('livewire.admin.promotions.index', [
            'promotions' => $promotions,
        ]);
    }
}
