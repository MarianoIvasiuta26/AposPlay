<x-layouts.app :title="__('AposPlay - Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl">

        {{-- Bienvenida --}}
        <div class="mb-2">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                Bienvenido, {{ auth()->user()->name }}
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                @switch(auth()->user()->role->value)
                    @case('superadmin')
                        Panel de Superadministrador — Acceso total al sistema
                        @break
                    @case('owner')
                        Panel de Owner — Gestiona tus complejos y canchas
                        @break
                    @case('staff')
                        Panel de Staff — Reservas y asistencia de tu complejo
                        @break
                    @default
                        Reserva canchas, consulta disponibilidad y acumula puntos
                @endswitch
            </p>
        </div>

        {{-- ============================================== --}}
        {{-- SUPERADMIN DASHBOARD --}}
        {{-- ============================================== --}}
        @if(auth()->user()->isSuperadmin())
            <div class="grid auto-rows-min gap-4 md:grid-cols-3">

                <a href="{{ route('admin.owners') }}"
                    class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 hover:shadow-md transition">
                    <div class="absolute inset-0 p-6 flex flex-col justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Gestionar Owners</h3>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Crear, activar o desactivar owners</p>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700 dark:bg-red-800 dark:text-red-100">Superadmin</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        </div>
                    </div>
                </a>

                <a href="{{ route('admin.daily-reservations') }}"
                    class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 hover:shadow-md transition">
                    <div class="absolute inset-0 p-6 flex flex-col justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Todas las Reservas</h3>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Ver reservas de todos los complejos</p>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700 dark:bg-red-800 dark:text-red-100">Superadmin</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                    </div>
                </a>

                <a href="{{ route('admin.promotions') }}"
                    class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 hover:shadow-md transition">
                    <div class="absolute inset-0 p-6 flex flex-col justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Promociones</h3>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Gestionar promociones activas</p>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-700 dark:bg-orange-700 dark:text-orange-100">Admin</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-600 dark:text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/></svg>
                        </div>
                    </div>
                </a>

            </div>

            <div class="grid auto-rows-min gap-4 md:grid-cols-3">

                <a href="{{ route('admin.coupons') }}"
                    class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 hover:shadow-md transition">
                    <div class="absolute inset-0 p-6 flex flex-col justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Cupones</h3>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Crear y gestionar cupones</p>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-700 dark:bg-orange-700 dark:text-orange-100">Admin</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                        </div>
                    </div>
                </a>

                <a href="{{ route('admin.occupancy-report') }}"
                    class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 hover:shadow-md transition">
                    <div class="absolute inset-0 p-6 flex flex-col justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Reportes</h3>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Ocupacion e ingresos</p>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-700 dark:bg-orange-700 dark:text-orange-100">Admin</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        </div>
                    </div>
                </a>

                <a href="{{ route('admin.income-export') }}"
                    class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 hover:shadow-md transition">
                    <div class="absolute inset-0 p-6 flex flex-col justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Exportar Ingresos</h3>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Descargar reportes CSV/PDF</p>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-700 dark:bg-orange-700 dark:text-orange-100">Admin</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                    </div>
                </a>

            </div>

            <div class="grid auto-rows-min gap-4 md:grid-cols-3">

                <a href="{{ route('admin.audit-log') }}"
                    class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 hover:shadow-md transition">
                    <div class="absolute inset-0 p-6 flex flex-col justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Auditoría</h3>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Registro de acciones del sistema</p>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700 dark:bg-red-800 dark:text-red-100">Superadmin</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                    </div>
                </a>

            </div>

        {{-- ============================================== --}}
        {{-- OWNER DASHBOARD --}}
        {{-- ============================================== --}}
        @elseif(auth()->user()->isOwner())
            <div class="grid auto-rows-min gap-4 md:grid-cols-3">

                <a href="{{ route('owner.complexes') }}"
                    class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 hover:shadow-md transition">
                    <div class="absolute inset-0 p-6 flex flex-col justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Mis Complejos</h3>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Gestiona tus complejos deportivos</p>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700 dark:bg-emerald-700 dark:text-emerald-100">Owner</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </div>
                    </div>
                </a>

                <a href="{{ route('canchas') }}"
                    class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 hover:shadow-md transition">
                    <div class="absolute inset-0 p-6 flex flex-col justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Mis Canchas</h3>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Administra y crea tus canchas</p>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700 dark:bg-emerald-700 dark:text-emerald-100">Owner</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A2 2 0 013 15.382V6.618a2 2 0 011.553-1.894L9 2m0 18l6-3m-6 3V2m6 15l5.447-2.724A2 2 0 0021 15.382V6.618a2 2 0 00-1.553-1.894L15 2m0 15V2m0 0L9 5"/></svg>
                        </div>
                    </div>
                </a>

                <a href="{{ route('owner.staff') }}"
                    class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 hover:shadow-md transition">
                    <div class="absolute inset-0 p-6 flex flex-col justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Mi Staff</h3>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Gestiona empleados de tus complejos</p>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700 dark:bg-emerald-700 dark:text-emerald-100">Owner</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                    </div>
                </a>

            </div>

            <div class="grid auto-rows-min gap-4 md:grid-cols-3">

                <a href="{{ route('admin.coupons') }}"
                    class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 hover:shadow-md transition">
                    <div class="absolute inset-0 p-6 flex flex-col justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Cupones</h3>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Crear cupones para clientes</p>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-700 dark:bg-orange-700 dark:text-orange-100">Admin</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                        </div>
                    </div>
                </a>

                <a href="{{ route('admin.occupancy-report') }}"
                    class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 hover:shadow-md transition">
                    <div class="absolute inset-0 p-6 flex flex-col justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Reporte de Ocupacion</h3>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Estadisticas de uso de canchas</p>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-700 dark:bg-orange-700 dark:text-orange-100">Admin</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        </div>
                    </div>
                </a>

                <a href="{{ route('admin.income-export') }}"
                    class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 hover:shadow-md transition">
                    <div class="absolute inset-0 p-6 flex flex-col justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Exportar Ingresos</h3>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Descargar reportes CSV/PDF</p>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-700 dark:bg-orange-700 dark:text-orange-100">Admin</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                    </div>
                </a>

                <a href="{{ route('admin.audit-log') }}"
                    class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 hover:shadow-md transition">
                    <div class="absolute inset-0 p-6 flex flex-col justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Auditoría</h3>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Acciones en tus complejos</p>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700 dark:bg-emerald-700 dark:text-emerald-100">Owner</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                    </div>
                </a>

            </div>

        {{-- ============================================== --}}
        {{-- STAFF DASHBOARD --}}
        {{-- ============================================== --}}
        @elseif(auth()->user()->isStaff())
            <div class="grid auto-rows-min gap-4 md:grid-cols-3">

                <a href="{{ route('staff.reservations') }}"
                    class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 hover:shadow-md transition">
                    <div class="absolute inset-0 p-6 flex flex-col justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Reservas del Dia</h3>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Ver y gestionar reservas de hoy</p>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700 dark:bg-blue-700 dark:text-blue-100">Staff</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                        </div>
                    </div>
                </a>

                <a href="{{ route('admin.promotions') }}"
                    class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 hover:shadow-md transition">
                    <div class="absolute inset-0 p-6 flex flex-col justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Promociones</h3>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Ver promociones activas</p>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700 dark:bg-blue-700 dark:text-blue-100">Staff</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-600 dark:text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/></svg>
                        </div>
                    </div>
                </a>

                <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                    <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
                </div>

            </div>

        {{-- ============================================== --}}
        {{-- USER DASHBOARD --}}
        {{-- ============================================== --}}
        @else
            <div class="grid auto-rows-min gap-4 md:grid-cols-3">

                <a href="{{ route('court-availability') }}"
                    class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 hover:shadow-md transition">
                    <div class="absolute inset-0 p-6 flex flex-col justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Reservar Cancha</h3>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Consulta disponibilidad y reserva</p>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-violet-100 text-violet-700 dark:bg-violet-700 dark:text-violet-100">Usuario</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                    </div>
                </a>

                <a href="{{ route('my-reservations') }}"
                    class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 hover:shadow-md transition">
                    <div class="absolute inset-0 p-6 flex flex-col justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Mis Reservas</h3>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Consulta, paga o cancela reservas</p>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-violet-100 text-violet-700 dark:bg-violet-700 dark:text-violet-100">Usuario</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        </div>
                    </div>
                </a>

                <a href="{{ route('loyalty-balance') }}"
                    class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 hover:shadow-md transition">
                    <div class="absolute inset-0 p-6 flex flex-col justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Mis Puntos</h3>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Acumula y canjea puntos de fidelidad</p>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-violet-100 text-violet-700 dark:bg-violet-700 dark:text-violet-100">Usuario</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-600 dark:text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                        </div>
                    </div>
                </a>

            </div>
        @endif

    </div>
</x-layouts.app>
