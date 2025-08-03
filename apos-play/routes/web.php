<?php

use App\Livewire\User\CourtAvailability;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::view('/', 'inicio');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');

    Route::get('/court-availability', CourtAvailability::class);
});

require __DIR__.'/auth.php';
