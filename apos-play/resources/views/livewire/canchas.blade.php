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
                        {{ $cancha->nombre }}
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

                <p class="text-sm text-gray-600 dark:text-neutral-400">
                    {{ $cancha->direccion }}
                </p>

                <div class="mt-4 flex justify-between items-center">
                    <span
                        class="px-3 py-1 rounded-full text-xs font-medium
                               bg-blue-100 text-blue-700
                               dark:bg-blue-900/40 dark:text-blue-300"
                    >
                        {{ ucfirst($cancha->tipo) }}
                    </span>

                    <span class="text-lg font-semibold text-gray-900 dark:text-neutral-100">
                        ${{ $cancha->precio }}
                    </span>
                </div>

                <p class="mt-3 text-sm text-gray-500 dark:text-neutral-400">
                    Jugadores: {{ $cancha->cantidad_jugadores }}
                </p>

                <livewire:cancha-horarios :cancha="$cancha" :key="'horarios-'.$cancha->id"/>
            </div>
        @endforeach
    </div>

    <!-- Formulario -->
    @if ($showForm)
        <div
            class="rounded-xl border shadow-sm p-6
                   bg-white border-neutral-200
                   dark:bg-neutral-800 dark:border-neutral-700"
        >
            <h3 class="text-lg font-semibold mb-6 text-gray-900 dark:text-neutral-100">
                {{ $isEditing ? 'Editar cancha' : 'Crear cancha' }}
            </h3>

            <form wire:submit.prevent="confirmarGuardado" class="grid grid-cols-1 md:grid-cols-2 gap-5">

                @foreach ([
                    'nombre' => 'Nombre',
                    'direccion' => 'Dirección',
                    'precio' => 'Precio',
                    'cantidad_jugadores' => 'Cantidad de jugadores'
                ] as $field => $label)
                    <div>
                        <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-neutral-300">
                            {{ $label }}
                        </label>
                        <input
                            type="{{ $field === 'precio' || $field === 'cantidad_jugadores' ? 'number' : 'text' }}"
                            wire:model="{{ $field }}"
                            class="w-full rounded-lg border px-3 py-2
                                   bg-white text-gray-900 border-neutral-300
                                   placeholder-gray-400
                                   focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500
                                   dark:bg-neutral-900 dark:text-neutral-100 dark:border-neutral-700 dark:placeholder-neutral-500"
                        >
                    </div>
                @endforeach

                <div>
                    <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-neutral-300">
                        Tipo
                    </label>
                    <select
                        wire:model="tipo"
                        class="w-full rounded-lg border px-3 py-2
                               bg-white text-gray-900 border-neutral-300
                               focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500
                               dark:bg-neutral-900 dark:text-neutral-100 dark:border-neutral-700"
                    >
                        <option value="">Seleccione tipo</option>
                        <option value="futbol">Fútbol</option>
                        <option value="padel">Pádel</option>
                    </select>
                </div>

                <div class="col-span-full flex justify-end gap-3 pt-6">
                    <button
                        type="button"
                        wire:click="$set('showForm', false)"
                        class="px-4 py-2 rounded-lg font-medium
                               bg-neutral-200 text-neutral-800 hover:bg-neutral-300
                               dark:bg-neutral-700 dark:text-neutral-200 dark:hover:bg-neutral-600 transition"
                    >
                        Cancelar
                    </button>

                    <button
                        type="submit"
                        class="px-5 py-2 rounded-lg font-medium
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
