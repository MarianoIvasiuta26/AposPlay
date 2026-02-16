<?php

use App\Livewire\User\CourtAvailability;
// use App\Livewire\User\ReservationForm;
use App\Livewire\Canchas;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');

    Route::get('/mis-reservas', App\Livewire\User\MyReservations::class)->name('my-reservations');

    // Admin routes (should be protected by role middleware in prod)
    Route::get('/admin/reservas-del-dia', App\Livewire\Admin\DailyReservations::class)->name('admin.daily-reservations');

    Route::get('/court-availability', CourtAvailability::class)->name('court-availability');

    // Mercado Pago Routes
    Route::get('/payment/create/{reservation}', [App\Http\Controllers\MercadoPagoController::class, 'createPreference'])->name('mercadopago.create');
    Route::get('/payment/success', [App\Http\Controllers\MercadoPagoController::class, 'success'])->name('mercadopago.success');
    Route::get('/payment/failure', [App\Http\Controllers\MercadoPagoController::class, 'failure'])->name('mercadopago.failure');
    Route::get('/payment/pending', [App\Http\Controllers\MercadoPagoController::class, 'pending'])->name('mercadopago.pending');
});

//Rutas de reservas
// Route::get('/reservar/{court}/{schedule}/{date}', ReservationForm::class)->name('reservations.create');

//Rutas de canchas
Route::get('/canchas', Canchas::class)->name('canchas');

require __DIR__ . '/auth.php';
