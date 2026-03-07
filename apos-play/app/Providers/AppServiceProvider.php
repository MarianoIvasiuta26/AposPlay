<?php

namespace App\Providers;

use App\Models\Promotion;
use App\Models\Reservation;
use App\Observers\ReservationObserver;
use App\Policies\PromotionPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Reservation::observe(ReservationObserver::class);
        Gate::policy(Promotion::class, PromotionPolicy::class);
    }
}
