<?php

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.auth')] class extends Component {
    public string $email = '';

    /**
     * Send a password reset link to the provided email address.
     */
    public function sendPasswordResetLink(): void
    {
        $this->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        Password::sendResetLink($this->only('email'));

        session()->flash('status', __('Se enviará un enlace de recuperación si la cuenta existe.'));
    }
}; ?>

<div class="flex flex-col gap-6">
    <x-auth-header :title="__('Recuperar contraseña')" :description="__('Ingresa tu email para recibir un enlace de recuperacion')" />

    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('status')" />

    <form wire:submit="sendPasswordResetLink" class="flex flex-col gap-6">
        <!-- Email Address -->
        <flux:input
            wire:model="email"
            :label="__('Email')"
            type="email"
            required
            autofocus
            placeholder="email@example.com"
        />

        <flux:button type="submit" class="w-full !bg-green-600 hover:!bg-green-500 !text-white font-semibold">{{ __('Enviar enlace de recuperacion') }}</flux:button>
    </form>

    <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-neutral-400">
        {{ __('O volver a') }}
        <flux:link :href="route('login')" wire:navigate class="!text-green-400 hover:!text-green-300">{{ __('iniciar sesion') }}</flux:link>
    </div>
</div>
