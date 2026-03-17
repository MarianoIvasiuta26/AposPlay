<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen antialiased bg-neutral-950 text-white font-[instrument-sans]">

        {{-- Navbar consistente con landing --}}
        <nav class="fixed top-0 inset-x-0 z-50 bg-black border-b border-neutral-800">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
                <a href="{{ route('home') }}" class="flex items-center gap-2">
                    <img src="{{ asset('img/aposplay-logo.png') }}" class="h-8 w-8" alt="AposPlay">
                    <span class="text-lg font-bold text-white">AposPlay</span>
                </a>

                <div class="flex items-center gap-3">
                    <a href="{{ route('login') }}"
                        class="px-4 py-2 text-sm font-medium text-neutral-300 hover:text-white border border-neutral-700 rounded-lg hover:border-neutral-500 transition">
                        Iniciar sesion
                    </a>
                    <a href="{{ route('register') }}"
                        class="px-5 py-2 text-sm font-semibold rounded-lg bg-red-600 text-white hover:bg-red-500 transition">
                        Registrarse
                    </a>
                </div>
            </div>
        </nav>

        {{-- Contenido centrado --}}
        <div class="flex min-h-svh flex-col items-center justify-center gap-6 p-6 md:p-10 pt-24">
            <div class="flex w-full max-w-sm flex-col gap-2">
                <a href="{{ route('home') }}" class="flex flex-col items-center gap-2 font-medium mb-4" wire:navigate>
                    <img src="{{ asset('img/aposplay-logo.png') }}" class="h-12 w-12" alt="AposPlay">
                    <span class="text-xl font-bold text-white">AposPlay</span>
                </a>
                <div class="flex flex-col gap-6">
                    {{ $slot }}
                </div>
            </div>
        </div>

        @fluxScripts
    </body>
</html>
