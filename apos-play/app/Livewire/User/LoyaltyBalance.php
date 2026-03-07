<?php

namespace App\Livewire\User;

use App\Models\LoyaltyPoint;
use App\Services\LoyaltyService;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class LoyaltyBalance extends Component
{
    public function render()
    {
        $loyaltyService = app(LoyaltyService::class);
        $balance = $loyaltyService->getBalance(auth()->user());

        $transactions = LoyaltyPoint::where('user_id', auth()->id())
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        return view('livewire.user.loyalty-balance', [
            'balance' => $balance,
            'transactions' => $transactions,
        ]);
    }
}
