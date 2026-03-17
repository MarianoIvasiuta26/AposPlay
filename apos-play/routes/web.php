<?php

use App\Livewire\User\CourtAvailability;
use App\Livewire\Canchas;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Authenticated user routes (all roles)
Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');

    // User-only routes (booking, points)
    Route::middleware(['role:user'])->group(function () {
        Route::get('/mis-reservas', App\Livewire\User\MyReservations::class)->name('my-reservations');
        Route::get('/mis-puntos', App\Livewire\User\LoyaltyBalance::class)->name('loyalty-balance');
        Route::get('/court-availability', CourtAvailability::class)->name('court-availability');
    });

    // Mercado Pago Routes (user needs these for payment flow)
    Route::get('/payment/create/{reservation}', [App\Http\Controllers\MercadoPagoController::class, 'createPreference'])->name('mercadopago.create');
    Route::get('/payment/success', [App\Http\Controllers\MercadoPagoController::class, 'success'])->name('mercadopago.success');
    Route::get('/payment/failure', [App\Http\Controllers\MercadoPagoController::class, 'failure'])->name('mercadopago.failure');
    Route::get('/payment/pending', [App\Http\Controllers\MercadoPagoController::class, 'pending'])->name('mercadopago.pending');
});

// Admin routes (superadmin + owner only)
Route::middleware(['auth', 'role:superadmin,owner'])->prefix('admin')->group(function () {
    Route::get('/reservas-del-dia', App\Livewire\Admin\DailyReservations::class)->name('admin.daily-reservations');
    Route::get('/cupones', App\Livewire\Admin\Coupons::class)->name('admin.coupons');
    Route::get('/reporte-ocupacion', App\Livewire\Admin\OccupancyReport::class)->name('admin.occupancy-report');
    Route::get('/exportar-ingresos', App\Livewire\Admin\IncomeExport::class)->name('admin.income-export');

    // UC-21: Superadmin manages owners
    Route::get('/owners', App\Livewire\Admin\Owners\Index::class)->name('admin.owners');
    Route::get('/owners/crear', App\Livewire\Admin\Owners\Form::class)->name('admin.owners.create');

    // UC-08: Court blocks
    Route::get('/bloqueos', App\Livewire\Admin\CourtBlocks\Index::class)->name('admin.court-blocks');
    Route::get('/bloqueos/crear', App\Livewire\Admin\CourtBlocks\Form::class)->name('admin.court-blocks.create');
});

// Shared admin routes (superadmin + owner + staff)
Route::middleware(['auth', 'role:superadmin,owner,staff'])->prefix('admin')->group(function () {
    Route::get('/promociones', App\Livewire\Admin\Promotions\Index::class)->name('admin.promotions');
    Route::get('/promociones/crear', App\Livewire\Admin\Promotions\Form::class)->name('admin.promotions.create');
    Route::get('/promociones/{promotion}/editar', App\Livewire\Admin\Promotions\Form::class)->name('admin.promotions.edit');
});

// Owner routes (superadmin + owner)
Route::middleware(['auth', 'role:superadmin,owner'])->prefix('owner')->group(function () {
    // UC-22: Staff management
    Route::get('/staff', App\Livewire\Owner\Staff\Index::class)->name('owner.staff');
    Route::get('/staff/crear', App\Livewire\Owner\Staff\Form::class)->name('owner.staff.create');

    // Complex management
    Route::get('/complejos', App\Livewire\Owner\Complexes\Index::class)->name('owner.complexes');
    Route::get('/complejos/crear', App\Livewire\Owner\Complexes\Form::class)->name('owner.complexes.create');
    Route::get('/complejos/{complex}/editar', App\Livewire\Owner\Complexes\Form::class)->name('owner.complexes.edit');
});

// Staff routes (superadmin + owner + staff) — reservas scoped por complejo
Route::middleware(['auth', 'role:superadmin,owner,staff'])->prefix('staff')->group(function () {
    Route::get('/reservas', App\Livewire\Staff\Reservations::class)->name('staff.reservations');
});

// UC-06: Gestionar canchas (admin/owner only)
Route::middleware(['auth', 'role:superadmin,owner'])->group(function () {
    Route::get('/canchas', Canchas::class)->name('canchas');
    // UC-07: Definir horarios de atención (standalone page)
    Route::get('/canchas/{court}/horarios', App\Livewire\CourtSchedules::class)->name('court.schedules');
});

require __DIR__ . '/auth.php';
