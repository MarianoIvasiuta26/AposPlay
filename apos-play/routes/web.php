<?php

use App\Livewire\User\CourtAvailability;
use App\Livewire\User\ReservationForm;
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

    Route::get('/court-availability', CourtAvailability::class)->name('court-availability');
});

//Rutas de reservas
Route::get('/reservar/{court}/{schedule}/{date}', ReservationForm::class)->name('reservations.create');


require __DIR__.'/auth.php';
