<div class="space-y-8">

    <!-- Header -->
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-semibold text-gray-900 dark:text-neutral-100">
            Mis canchas
        </h2>

        <button
            wire:click="crearCancha"
            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg
                   bg-green-600 hover:bg-green-700 active:bg-green-800
                   text-white font-medium shadow-sm
                   focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2
                   dark:focus:ring-offset-neutral-900 transition"
        >
            Crear cancha
        </button>
    </div>

    <!-- Mensaje -->
    @if (session()->has('success'))
        <div class="rounded-lg border p-4
                    bg-green-50 border-green-200 text-green-800
                    dark:bg-green-900/20 dark:border-green-800 dark:text-green-300">
            {{ session('success') }}
        </div>
    @endif

    <!-- Listado -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach ($canchas as $cancha)
            <div
                class="rounded-xl border shadow-sm p-5
                       bg-white border-neutral-200
                       dark:bg-neutral-800 dark:border-neutral-700
                       hover:shadow-md transition"
            >
                <div class="flex justify-between items-start mb-3">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-neutral-100">
                        {{ $cancha->name }}
                    </h3>

                    <button
                        wire:click="editarCancha({{ $cancha->id }})"
                        class="px-3 py-1.5 rounded-md text-sm font-medium
                               bg-blue-600 hover:bg-blue-700 active:bg-blue-800
                               text-white shadow-sm
                               focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2
                               dark:focus:ring-offset-neutral-800 transition"
                    >
                        Editar
                    </button>
                </div>

                <div class="text-sm text-gray-600 dark:text-neutral-400 mb-4">
                    @if($cancha->address)
                        <p>{{ $cancha->address->street }} {{ $cancha->address->number }}</p>
                        <p>{{ $cancha->address->city }}, {{ $cancha->address->province }}</p>
                        <p>{{ $cancha->address->country }} ({{ $cancha->address->zip_code }})</p>
                    @else
                        <span class="italic text-gray-400">Sin dirección</span>
                    @endif
                </div>

                <div class="mt-4 flex justify-between items-center">
                    <span
                        class="px-3 py-1 rounded-full text-xs font-medium
                               bg-blue-100 text-blue-700
                               dark:bg-blue-900/40 dark:text-blue-300"
                    >
                        {{ ucfirst($cancha->type) }}
                    </span>

                    <span class="text-lg font-semibold text-gray-900 dark:text-neutral-100">
                        ${{ $cancha->price }}
                    </span>
                </div>

                <p class="mt-3 text-sm text-gray-500 dark:text-neutral-400">
                    Jugadores: {{ $cancha->number_players }}
                </p>

                <livewire:court-schedules :court="$cancha" :key="'schedules-'.$cancha->id"/>
            </div>
        @endforeach
    </div>

    <!-- Modal Formulario -->
    @if ($showForm)
        <div class="fixed inset-0 z-40 flex items-center justify-center overflow-y-auto overflow-x-hidden p-4 backdrop-blur-sm bg-black/30">
            <div class="relative w-full max-w-4xl rounded-xl bg-white p-6 shadow-2xl dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700">
                
                {{-- Mock Header --}}
                <div class="mb-5 flex items-center justify-between">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">
                        {{ $isEditing ? 'Editar Cancha' : 'Nueva Cancha' }}
                    </h3>
                    <button wire:click="$set('showForm', false)" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form wire:submit.prevent="confirmarGuardado" class="space-y-6 max-h-[70vh] overflow-y-auto pr-2">
                    
                    <!-- Datos Básicos -->
                    <div>
                        <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4 border-b pb-2 dark:border-neutral-700">
                            Información Básica
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <!-- Nombre -->
                            <div>
                                <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-neutral-300">
                                    Nombre
                                </label>
                                <input type="text" wire:model="nombre" class="w-full rounded-lg border px-3 py-2 bg-white text-gray-900 border-neutral-300 focus:outline-none focus:ring-2 focus:ring-green-500 dark:bg-neutral-900 dark:text-neutral-100 dark:border-neutral-700">
                                @error('nombre') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
                            </div>

                            <!-- Precio -->
                            <div>
                                <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-neutral-300">
                                    Precio
                                </label>
                                <input type="number" wire:model="precio" class="w-full rounded-lg border px-3 py-2 bg-white text-gray-900 border-neutral-300 focus:outline-none focus:ring-2 focus:ring-green-500 dark:bg-neutral-900 dark:text-neutral-100 dark:border-neutral-700">
                                @error('precio') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
                            </div>

                            <!-- Tipo -->
                            <div>
                                <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-neutral-300">
                                    Tipo
                                </label>
                                <select wire:model="tipo" class="w-full rounded-lg border px-3 py-2 bg-white text-gray-900 border-neutral-300 focus:outline-none focus:ring-2 focus:ring-green-500 dark:bg-neutral-900 dark:text-neutral-100 dark:border-neutral-700">
                                    <option value="">Seleccione tipo</option>
                                    <option value="futbol">Fútbol</option>
                                    <option value="padel">Pádel</option>
                                </select>
                                @error('tipo') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
                            </div>

                            <!-- Cantidad Jugadores -->
                            <div>
                                <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-neutral-300">
                                    Cantidad de jugadores
                                </label>
                                <input type="number" wire:model="cantidad_jugadores" class="w-full rounded-lg border px-3 py-2 bg-white text-gray-900 border-neutral-300 focus:outline-none focus:ring-2 focus:ring-green-500 dark:bg-neutral-900 dark:text-neutral-100 dark:border-neutral-700">
                                @error('cantidad_jugadores') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Dirección -->
                    <div>
                        <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4 border-b pb-2 dark:border-neutral-700">
                            Dirección
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <!-- Calle -->
                            <div class="md:col-span-1">
                                <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-neutral-300">
                                    Calle
                                </label>
                                <input type="text" wire:model="street" class="w-full rounded-lg border px-3 py-2 bg-white text-gray-900 border-neutral-300 focus:outline-none focus:ring-2 focus:ring-green-500 dark:bg-neutral-900 dark:text-neutral-100 dark:border-neutral-700">
                                @error('street') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
                            </div>

                            <!-- Número -->
                            <div>
                                <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-neutral-300">
                                    Número
                                </label>
                                <input type="text" wire:model="number" class="w-full rounded-lg border px-3 py-2 bg-white text-gray-900 border-neutral-300 focus:outline-none focus:ring-2 focus:ring-green-500 dark:bg-neutral-900 dark:text-neutral-100 dark:border-neutral-700">
                                @error('number') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
                            </div>

                            <!-- Ciudad -->
                            <div>
                                <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-neutral-300">
                                    Ciudad
                                </label>
                                <input type="text" wire:model="city" class="w-full rounded-lg border px-3 py-2 bg-white text-gray-900 border-neutral-300 focus:outline-none focus:ring-2 focus:ring-green-500 dark:bg-neutral-900 dark:text-neutral-100 dark:border-neutral-700">
                                @error('city') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
                            </div>

                            <!-- Provincia -->
                            <div>
                                <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-neutral-300">
                                    Provincia
                                </label>
                                <input type="text" wire:model="province" class="w-full rounded-lg border px-3 py-2 bg-white text-gray-900 border-neutral-300 focus:outline-none focus:ring-2 focus:ring-green-500 dark:bg-neutral-900 dark:text-neutral-100 dark:border-neutral-700">
                                @error('province') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
                            </div>

                            <!-- Código Postal -->
                            <div>
                                <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-neutral-300">
                                    Código Postal
                                </label>
                                <input type="text" wire:model="zip_code" class="w-full rounded-lg border px-3 py-2 bg-white text-gray-900 border-neutral-300 focus:outline-none focus:ring-2 focus:ring-green-500 dark:bg-neutral-900 dark:text-neutral-100 dark:border-neutral-700">
                                @error('zip_code') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
                            </div>

                            <!-- País -->
                            <div>
                                <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-neutral-300">
                                    País
                                </label>
                                <input type="text" wire:model="country" class="w-full rounded-lg border px-3 py-2 bg-white text-gray-900 border-neutral-300 focus:outline-none focus:ring-2 focus:ring-green-500 dark:bg-neutral-900 dark:text-neutral-100 dark:border-neutral-700">
                                @error('country') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Botones -->
                    <div class="flex justify-end gap-3 pt-6 border-t border-gray-100 dark:border-zinc-800">
                        <button
                            type="button"
                            wire:click="$set('showForm', false)"
                            class="px-4 py-2 rounded-lg font-medium text-sm
                                   bg-neutral-200 text-neutral-800 hover:bg-neutral-300
                                   dark:bg-zinc-800 dark:text-neutral-200 dark:hover:bg-zinc-700 transition"
                        >
                            Cancelar
                        </button>

                        <button
                            type="submit"
                            class="px-5 py-2 rounded-lg font-medium text-sm
                                   bg-green-600 hover:bg-green-700 active:bg-green-800
                                   text-white shadow-sm
                                   focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2
                                   dark:focus:ring-offset-neutral-800 transition"
                        >
                            {{ $isEditing ? 'Actualizar cancha' : 'Guardar cancha' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Confirmación -->
    @if ($showConfirmModal)
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50">
            <div
                class="w-full max-w-md rounded-xl p-6 shadow-xl
                       bg-white dark:bg-neutral-800"
            >
                <p class="mb-6 text-center text-gray-900 dark:text-neutral-100">
                    ¿Está seguro de {{ $isEditing ? 'actualizar' : 'guardar' }} la cancha?
                </p>

                <div class="flex justify-end gap-3">
                    <button
                        wire:click="$set('showConfirmModal', false)"
                        class="px-4 py-2 rounded-lg
                               bg-neutral-200 text-neutral-800 hover:bg-neutral-300
                               dark:bg-neutral-700 dark:text-neutral-200 dark:hover:bg-neutral-600 transition"
                    >
                        Cancelar
                    </button>

                    <button
                        wire:click="guardarCancha"
                        class="px-4 py-2 rounded-lg
                               bg-green-600 hover:bg-green-700 active:bg-green-800
                               text-white shadow-sm transition"
                    >
                        Sí, confirmar
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>
