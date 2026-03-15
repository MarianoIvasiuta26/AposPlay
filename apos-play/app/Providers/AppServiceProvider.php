<?php

namespace App\Providers;

use App\Models\Complex;
use App\Models\CourtBlock;
use App\Models\Promotion;
use App\Models\Reservation;
use App\Observers\ReservationObserver;
use App\Policies\ComplexPolicy;
use App\Policies\CourtBlockPolicy;
use App\Policies\PromotionPolicy;
use App\Policies\ReservationPolicy;
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
        Gate::policy(Complex::class, ComplexPolicy::class);
        Gate::policy(Reservation::class, ReservationPolicy::class);
        Gate::policy(CourtBlock::class, CourtBlockPolicy::class);
    }
}
