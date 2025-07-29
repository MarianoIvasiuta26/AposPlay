<?php

use App\Livewire\User\CourtAvailability;
use Illuminate\Support\Facades\Route;

Route::view('/', 'inicio');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::get('/court-availability', CourtAvailability::class);

require __DIR__.'/auth.php';
