<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>AposPlay - Reserva tu cancha</title>
    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased font-[instrument-sans] bg-neutral-950 text-white">

    {{-- ===================== NAVBAR ===================== --}}
    <nav class="fixed top-0 inset-x-0 z-50 bg-black border-b border-neutral-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 grid grid-cols-3 items-center">
            {{-- Logo izquierda --}}
            <div class="flex items-center">
                <a href="{{ route('home') }}" class="flex items-center gap-2">
                    <img src="{{ asset('img/aposplay-logo.png') }}" class="h-8 w-8" alt="AposPlay">
                    <span class="text-lg font-bold text-white">AposPlay</span>
                </a>
            </div>

            {{-- Links centro --}}
            <div class="hidden lg:flex items-center justify-center gap-8 text-sm font-medium">
                <a href="#como-funciona" class="text-neutral-300 hover:text-green-400 transition">Como funciona</a>
                <a href="#canchas" class="text-neutral-300 hover:text-green-400 transition">Canchas</a>
                <a href="#ventajas" class="text-neutral-300 hover:text-green-400 transition">Ventajas</a>
                <a href="#owner" class="text-neutral-300 hover:text-green-400 transition">Tenes un complejo?</a>
            </div>

            {{-- Auth derecha --}}
            <div class="hidden lg:flex items-center justify-end gap-3">
                @auth
                    <a href="{{ url('/dashboard') }}"
                        class="px-5 py-2 text-sm font-semibold rounded-lg bg-green-600 text-white hover:bg-green-500 transition">
                        Ir al Panel
                    </a>
                @else
                    <a href="{{ route('login') }}"
                        class="px-4 py-2 text-sm font-medium text-neutral-300 hover:text-white border border-neutral-700 rounded-lg hover:border-neutral-500 transition">
                        Iniciar sesion
                    </a>
                    <a href="{{ route('register') }}"
                        class="px-5 py-2 text-sm font-semibold rounded-lg bg-red-600 text-white hover:bg-red-500 transition">
                        Registrarse
                    </a>
                @endauth
            </div>

            {{-- Mobile menu button --}}
            <div class="lg:hidden col-span-2 flex justify-end">
                <button onclick="document.getElementById('mobile-menu').classList.toggle('hidden')" class="text-white p-2">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                </button>
            </div>
        </div>

        {{-- Mobile menu --}}
        <div id="mobile-menu" class="hidden lg:hidden bg-black border-t border-neutral-800 px-4 py-4 space-y-3">
            <a href="#como-funciona" class="block text-neutral-300 hover:text-green-400 text-sm font-medium py-2">Como funciona</a>
            <a href="#canchas" class="block text-neutral-300 hover:text-green-400 text-sm font-medium py-2">Canchas</a>
            <a href="#ventajas" class="block text-neutral-300 hover:text-green-400 text-sm font-medium py-2">Ventajas</a>
            <a href="#owner" class="block text-neutral-300 hover:text-green-400 text-sm font-medium py-2">Tenes un complejo?</a>
            <div class="pt-3 border-t border-neutral-800 flex flex-col gap-2">
                @auth
                    <a href="{{ url('/dashboard') }}" class="px-4 py-2.5 text-sm font-semibold rounded-lg bg-green-600 text-white text-center">Ir al Panel</a>
                @else
                    <a href="{{ route('login') }}" class="px-4 py-2.5 text-sm font-medium text-neutral-300 border border-neutral-700 rounded-lg text-center">Iniciar sesion</a>
                    <a href="{{ route('register') }}" class="px-4 py-2.5 text-sm font-semibold rounded-lg bg-red-600 text-white text-center">Registrarse</a>
                @endauth
            </div>
        </div>
    </nav>

    {{-- ===================== HERO ===================== --}}
    <section class="relative pt-16 min-h-screen flex items-center overflow-hidden" style="background: linear-gradient(160deg, #000000 0%, #031a09 35%, #0a0a0a 50%, #1a0505 70%, #000000 100%);">
        <div class="absolute top-20 right-10 w-[500px] h-[500px] bg-green-500/15 rounded-full blur-[150px]"></div>
        <div class="absolute bottom-20 left-10 w-[400px] h-[400px] bg-red-600/10 rounded-full blur-[130px]"></div>

        <div class="relative w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 sm:py-28">
            <div class="max-w-4xl mx-auto text-center">
                <div class="inline-flex items-center gap-2 px-5 py-2 rounded-full bg-green-500/10 border border-green-500/30 mb-10">
                    <span class="w-2 h-2 rounded-full bg-green-400 animate-pulse"></span>
                    <span class="text-sm font-medium text-green-300">Apostoles, Misiones · Reservas abiertas 24/7</span>
                </div>

                <h1 class="text-5xl sm:text-7xl lg:text-8xl font-bold tracking-tight leading-[1.05]">
                    <span class="text-white">Reserva tu cancha</span>
                    <br>
                    <span class="text-green-400">en minutos</span>
                </h1>
                <p class="mt-8 text-lg sm:text-xl text-neutral-400 max-w-2xl mx-auto leading-relaxed">
                    La plataforma de canchas de futbol y padel de <span class="text-white font-medium">Apostoles, Misiones</span>. Consulta disponibilidad en tiempo real y reserva tu horario al instante con pago seguro via Mercado Pago.
                </p>
                <div class="mt-12 flex flex-col sm:flex-row items-center justify-center gap-4">
                    <a href="#canchas"
                        class="w-full sm:w-auto px-10 py-4 text-lg font-semibold rounded-xl bg-green-600 text-white hover:bg-green-500 transition-all shadow-lg shadow-green-600/30">
                        Ver canchas disponibles
                    </a>
                    @guest
                        <a href="{{ route('register') }}"
                            class="w-full sm:w-auto px-10 py-4 text-lg font-semibold rounded-xl bg-red-600 text-white hover:bg-red-500 transition-all shadow-lg shadow-red-600/30">
                            Crear cuenta gratis
                        </a>
                    @endguest
                </div>
            </div>

            {{-- Stats --}}
            <div class="mt-20 sm:mt-24 grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5 max-w-4xl mx-auto">
                @php
                    $totalCourts = \App\Models\Court::count();
                    $totalComplexes = \App\Models\Complex::where('active', true)->count();
                    $totalUsers = \App\Models\User::where('role', 'user')->count();
                    $totalReservations = \App\Models\Reservation::count();
                @endphp
                <div class="rounded-xl border border-green-500/30 bg-green-950/30 p-5 sm:p-6 text-center">
                    <p class="text-3xl sm:text-4xl font-bold text-green-400">{{ $totalCourts }}</p>
                    <p class="mt-1 text-sm text-neutral-400">Canchas</p>
                </div>
                <div class="rounded-xl border border-neutral-700 bg-neutral-900/50 p-5 sm:p-6 text-center">
                    <p class="text-3xl sm:text-4xl font-bold text-white">{{ $totalComplexes }}</p>
                    <p class="mt-1 text-sm text-neutral-400">Complejos</p>
                </div>
                <div class="rounded-xl border border-red-500/30 bg-red-950/30 p-5 sm:p-6 text-center">
                    <p class="text-3xl sm:text-4xl font-bold text-red-400">{{ $totalUsers }}+</p>
                    <p class="mt-1 text-sm text-neutral-400">Jugadores</p>
                </div>
                <div class="rounded-xl border border-neutral-700 bg-neutral-900/50 p-5 sm:p-6 text-center">
                    <p class="text-3xl sm:text-4xl font-bold text-white">{{ $totalReservations }}+</p>
                    <p class="mt-1 text-sm text-neutral-400">Reservas</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ===================== COMO FUNCIONA ===================== --}}
    <section id="como-funciona" class="py-24 sm:py-32 bg-neutral-950">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16 sm:mb-20">
                <span class="inline-block px-4 py-1.5 rounded-full bg-green-500/10 border border-green-500/30 text-green-400 text-xs font-bold uppercase tracking-wider mb-5">Paso a paso</span>
                <h2 class="text-3xl sm:text-5xl font-bold text-white">Como funciona</h2>
                <p class="mt-4 text-lg text-neutral-400 max-w-xl mx-auto">Reservar tu cancha es simple, rapido y seguro</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 sm:gap-8 max-w-5xl mx-auto">
                <div class="relative bg-neutral-900 rounded-2xl p-7 sm:p-8 border border-neutral-800 text-center hover:border-green-500/40 transition-all group">
                    <div class="absolute -top-3.5 left-1/2 -translate-x-1/2 bg-green-600 text-white text-sm font-bold rounded-full w-8 h-8 flex items-center justify-center">1</div>
                    <div class="w-16 h-16 mx-auto flex items-center justify-center rounded-xl bg-green-600 text-white mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-3">Crea tu cuenta</h3>
                    <p class="text-base text-neutral-400 leading-relaxed">Registrate gratis en segundos con tu nombre y email. Sin compromisos ni costos ocultos.</p>
                </div>

                <div class="relative bg-neutral-900 rounded-2xl p-7 sm:p-8 border border-neutral-800 text-center hover:border-red-500/40 transition-all group">
                    <div class="absolute -top-3.5 left-1/2 -translate-x-1/2 bg-red-600 text-white text-sm font-bold rounded-full w-8 h-8 flex items-center justify-center">2</div>
                    <div class="w-16 h-16 mx-auto flex items-center justify-center rounded-xl bg-red-600 text-white mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-3">Busca y elige</h3>
                    <p class="text-base text-neutral-400 leading-relaxed">Explora canchas por tipo y ubicacion. Consulta la disponibilidad en tiempo real para los proximos 7 dias.</p>
                </div>

                <div class="relative bg-neutral-900 rounded-2xl p-7 sm:p-8 border border-neutral-800 text-center hover:border-green-500/40 transition-all group">
                    <div class="absolute -top-3.5 left-1/2 -translate-x-1/2 bg-green-600 text-white text-sm font-bold rounded-full w-8 h-8 flex items-center justify-center">3</div>
                    <div class="w-16 h-16 mx-auto flex items-center justify-center rounded-xl bg-green-600 text-white mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-3">Reserva y paga</h3>
                    <p class="text-base text-neutral-400 leading-relaxed">Confirma tu reserva y paga de forma segura con Mercado Pago. Recibiras recordatorios antes de tu partido.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ===================== CANCHAS ===================== --}}
    <section id="canchas" class="py-24 sm:py-32 bg-neutral-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-14 sm:mb-18">
                <span class="inline-block px-4 py-1.5 rounded-full bg-red-500/10 border border-red-500/30 text-red-400 text-xs font-bold uppercase tracking-wider mb-5">Explora</span>
                <h2 class="text-3xl sm:text-5xl font-bold text-white">Canchas disponibles</h2>
                <p class="mt-4 text-lg text-neutral-400 max-w-xl mx-auto">Encontra la cancha ideal para tu proximo partido</p>
            </div>

            @php
                $courts = \App\Models\Court::with('address')->whereNull('deleted_at')->get();
            @endphp

            @if($courts->isEmpty())
                <div class="text-center py-12 text-neutral-500 text-lg">No hay canchas disponibles en este momento.</div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 sm:gap-6">
                    @foreach($courts as $court)
                        @php $isFutbol = $court->type === 'futbol'; @endphp
                        <div class="group bg-neutral-950 rounded-xl overflow-hidden border border-neutral-800 hover:border-{{ $isFutbol ? 'green' : 'red' }}-500/50 transition-all duration-300 hover:-translate-y-1">
                            <div class="h-1.5 {{ $isFutbol ? 'bg-green-600' : 'bg-red-600' }}"></div>
                            <div class="p-5 sm:p-6">
                                <div class="flex items-start justify-between mb-3">
                                    <h3 class="text-lg font-bold text-white">{{ $court->name }}</h3>
                                    <span class="shrink-0 ml-2 inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold {{ $isFutbol ? 'bg-green-500/15 text-green-400 border border-green-500/30' : 'bg-red-500/15 text-red-400 border border-red-500/30' }}">
                                        {{ ucfirst($court->type) }}
                                    </span>
                                </div>
                                @if($court->address)
                                    <div class="flex items-center gap-2 text-sm text-neutral-400 mb-3">
                                        <svg class="w-4 h-4 {{ $isFutbol ? 'text-green-500' : 'text-red-500' }} shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                                        </svg>
                                        <span class="truncate">{{ $court->address->street }} {{ $court->address->number }}, {{ $court->address->city }}</span>
                                    </div>
                                @endif
                                <div class="flex flex-wrap items-center gap-2 mb-4">
                                    <span class="inline-flex items-center gap-1.5 text-xs text-neutral-300 bg-neutral-800 rounded-lg px-3 py-1.5">
                                        <svg class="w-3.5 h-3.5 text-neutral-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                                        </svg>
                                        {{ $court->number_players }} jugadores
                                    </span>
                                    @if($court->address)
                                        <span class="inline-flex items-center text-xs text-neutral-300 bg-neutral-800 rounded-lg px-3 py-1.5">
                                            {{ $court->address->province ?? $court->address->city }}
                                        </span>
                                    @endif
                                </div>
                                <div class="flex items-center justify-between pt-4 border-t border-neutral-800">
                                    <div>
                                        <p class="text-2xl font-bold text-white">${{ number_format($court->price, 0, ',', '.') }}</p>
                                        <p class="text-xs text-neutral-500">por hora</p>
                                    </div>
                                    @auth
                                        <a href="{{ route('court-availability') }}" class="px-5 py-2.5 text-sm font-semibold rounded-lg {{ $isFutbol ? 'bg-green-600 hover:bg-green-500' : 'bg-red-600 hover:bg-red-500' }} text-white transition">Reservar</a>
                                    @else
                                        <a href="{{ route('register') }}" class="px-5 py-2.5 text-sm font-semibold rounded-lg {{ $isFutbol ? 'bg-green-600 hover:bg-green-500' : 'bg-red-600 hover:bg-red-500' }} text-white transition">Registrate</a>
                                    @endauth
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                @guest
                    <div class="mt-16 sm:mt-24 rounded-2xl p-10 sm:p-14 text-center border border-neutral-800" style="background: linear-gradient(135deg, #052e16 0%, #0a0a0a 50%, #1a0505 100%);">
                        <h3 class="text-2xl sm:text-4xl font-bold text-white">Queres reservar una cancha?</h3>
                        <p class="mt-4 sm:mt-5 text-neutral-400 max-w-lg mx-auto text-base sm:text-lg">
                            Crea tu cuenta gratuita para acceder a la disponibilidad en tiempo real, reservar al instante y acumular puntos de fidelidad.
                        </p>
                        <div class="mt-8 sm:mt-10 flex flex-col sm:flex-row items-center justify-center gap-4">
                            <a href="{{ route('register') }}" class="w-full sm:w-auto px-10 py-4 text-lg font-bold rounded-xl bg-green-600 text-white hover:bg-green-500 transition">Crear cuenta gratis</a>
                            <a href="{{ route('login') }}" class="w-full sm:w-auto px-10 py-4 text-lg font-medium rounded-xl border border-neutral-600 text-neutral-300 hover:border-white hover:text-white transition">Ya tengo cuenta</a>
                        </div>
                    </div>
                @endguest
            @endif
        </div>
    </section>

    {{-- ===================== BENEFICIOS ===================== --}}
    <section id="ventajas" class="py-24 sm:py-32 bg-neutral-950">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-14 sm:mb-18">
                <span class="inline-block px-4 py-1.5 rounded-full bg-green-500/10 border border-green-500/30 text-green-400 text-xs font-bold uppercase tracking-wider mb-5">Ventajas</span>
                <h2 class="text-3xl sm:text-5xl font-bold text-white">Por que elegir AposPlay</h2>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 sm:gap-6">
                <div class="p-7 rounded-xl bg-neutral-900 border border-neutral-800 hover:border-green-500/40 transition-all group">
                    <div class="w-14 h-14 flex items-center justify-center rounded-xl bg-green-600 text-white mb-5 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2">Disponibilidad 24/7</h3>
                    <p class="text-base text-neutral-400 leading-relaxed">Consulta y reserva en cualquier momento, los proximos 7 dias.</p>
                </div>
                <div class="p-7 rounded-xl bg-neutral-900 border border-neutral-800 hover:border-red-500/40 transition-all group">
                    <div class="w-14 h-14 flex items-center justify-center rounded-xl bg-red-600 text-white mb-5 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2">Pago seguro</h3>
                    <p class="text-base text-neutral-400 leading-relaxed">Paga con Mercado Pago, la plataforma mas confiable de Argentina.</p>
                </div>
                <div class="p-7 rounded-xl bg-neutral-900 border border-neutral-800 hover:border-green-500/40 transition-all group">
                    <div class="w-14 h-14 flex items-center justify-center rounded-xl bg-green-600 text-white mb-5 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2">Programa de puntos</h3>
                    <p class="text-base text-neutral-400 leading-relaxed">Acumula puntos con cada reserva y canjealos por descuentos.</p>
                </div>
                <div class="p-7 rounded-xl bg-neutral-900 border border-neutral-800 hover:border-red-500/40 transition-all group">
                    <div class="w-14 h-14 flex items-center justify-center rounded-xl bg-red-600 text-white mb-5 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2">Recordatorios</h3>
                    <p class="text-base text-neutral-400 leading-relaxed">Recibis notificaciones por email antes de cada partido.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ===================== OWNER CTA ===================== --}}
    <section id="owner" class="py-24 sm:py-32 bg-neutral-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-14 sm:mb-18">
                <span class="inline-block px-4 py-1.5 rounded-full bg-red-500/10 border border-red-500/30 text-red-400 text-xs font-bold uppercase tracking-wider mb-5">Para duenos de complejos</span>
                <h2 class="text-3xl sm:text-5xl font-bold text-white">Tenes un complejo en Apostoles?</h2>
                <p class="mt-4 text-lg text-neutral-400 max-w-2xl mx-auto">
                    Suma tus canchas a AposPlay y accede a un panel completo para gestionar reservas, staff, reportes de ocupacion e ingresos. Solo para complejos de <span class="text-white font-medium">Apostoles, Misiones</span>.
                </p>
            </div>

            {{-- Features grid --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-16">
                <div class="flex items-start gap-3 p-5 rounded-xl bg-neutral-950 border border-neutral-800">
                    <div class="w-8 h-8 rounded-full bg-green-500/20 flex items-center justify-center shrink-0 mt-0.5">
                        <svg class="w-4 h-4 text-green-400" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                        </svg>
                    </div>
                    <span class="text-neutral-300 text-sm">Panel de administracion con reportes de ocupacion e ingresos</span>
                </div>
                <div class="flex items-start gap-3 p-5 rounded-xl bg-neutral-950 border border-neutral-800">
                    <div class="w-8 h-8 rounded-full bg-green-500/20 flex items-center justify-center shrink-0 mt-0.5">
                        <svg class="w-4 h-4 text-green-400" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                        </svg>
                    </div>
                    <span class="text-neutral-300 text-sm">Gestion de staff con permisos por complejo</span>
                </div>
                <div class="flex items-start gap-3 p-5 rounded-xl bg-neutral-950 border border-neutral-800">
                    <div class="w-8 h-8 rounded-full bg-green-500/20 flex items-center justify-center shrink-0 mt-0.5">
                        <svg class="w-4 h-4 text-green-400" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                        </svg>
                    </div>
                    <span class="text-neutral-300 text-sm">Bloqueo de horarios por mantenimiento o eventos</span>
                </div>
                <div class="flex items-start gap-3 p-5 rounded-xl bg-neutral-950 border border-neutral-800">
                    <div class="w-8 h-8 rounded-full bg-green-500/20 flex items-center justify-center shrink-0 mt-0.5">
                        <svg class="w-4 h-4 text-green-400" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                        </svg>
                    </div>
                    <span class="text-neutral-300 text-sm">Cupones, promociones y cobros via Mercado Pago</span>
                </div>
            </div>

            {{-- Contact form card - full width --}}
            <div class="bg-neutral-950 rounded-2xl p-8 sm:p-12 border border-neutral-800">
                <div class="max-w-3xl mx-auto">
                    @livewire('owner-contact')
                </div>
            </div>
        </div>
    </section>

    {{-- ===================== FOOTER ===================== --}}
    <footer class="bg-black border-t border-neutral-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-12">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-6">
                <div class="flex items-center gap-2">
                    <img src="{{ asset('img/aposplay-logo.png') }}" class="h-7 w-7" alt="AposPlay">
                    <span class="text-base font-bold text-white">AposPlay</span>
                </div>
                <div class="flex flex-wrap gap-4 sm:gap-6 text-sm text-neutral-400">
                    <a href="#como-funciona" class="hover:text-green-400 transition">Como funciona</a>
                    <a href="#canchas" class="hover:text-green-400 transition">Canchas</a>
                    <a href="#ventajas" class="hover:text-green-400 transition">Ventajas</a>
                    <a href="#owner" class="hover:text-red-400 transition">Para complejos</a>
                    @guest
                        <a href="{{ route('register') }}" class="hover:text-red-400 transition">Registrarse</a>
                    @endguest
                </div>
                <p class="text-sm text-neutral-600">&copy; {{ date('Y') }} AposPlay. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>


</body>
</html>
