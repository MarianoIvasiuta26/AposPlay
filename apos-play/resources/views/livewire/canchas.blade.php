<div class="space-y-6">

    <!-- Header -->
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-semibold text-gray-900 dark:text-white">
            Mis canchas
        </h2>

        <button
            wire:click="crearCancha"
            class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg shadow-sm transition"
        >
            Crear cancha
        </button>
    </div>

    <!-- Mensaje -->
    @if (session()->has('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 dark:bg-green-900/30 dark:border-green-800 dark:text-green-300 p-4 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <!-- Listado -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach ($canchas as $cancha)
            <div class="bg-white dark:bg-neutral-800 rounded-xl border border-neutral-200 dark:border-neutral-700 shadow-sm p-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    {{ $cancha->nombre }}
                </h3>

                <p class="text-sm text-gray-600 dark:text-gray-400">
                    {{ $cancha->direccion }}
                </p>

                <div class="mt-3 flex justify-between items-center">
                    <span class="px-2 py-1 rounded-full text-xs bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                        {{ ucfirst($cancha->tipo) }}
                    </span>

                    <span class="font-semibold text-gray-900 dark:text-white">
                        ${{ $cancha->precio }}
                    </span>
                </div>

                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                    Jugadores: {{ $cancha->cantidad_jugadores }}
                </p>
            </div>
        @endforeach
    </div>

    <!-- Formulario -->
    @if ($showForm)
        <div class="bg-white dark:bg-neutral-800 rounded-xl border border-neutral-200 dark:border-neutral-700 p-6 shadow-sm">
            <h3 class="text-lg font-semibold mb-6 text-gray-900 dark:text-white">
                Crear cancha
            </h3>

            <form wire:submit.prevent="confirmarGuardado" class="grid grid-cols-1 md:grid-cols-2 gap-5">

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Nombre
                    </label>
                    <input
                        type="text"
                        wire:model="nombre"
                        placeholder="Ej: Cancha Central"
                        class="w-full rounded-lg border bg-white text-gray-900 border-gray-300 placeholder-gray-400
                               focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500
                               dark:bg-neutral-900 dark:text-white dark:border-neutral-700 dark:placeholder-gray-500"
                    >
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Dirección
                    </label>
                    <input
                        type="text"
                        wire:model="direccion"
                        placeholder="Ej: Av. Siempre Viva 123"
                        class="w-full rounded-lg border bg-white text-gray-900 border-gray-300 placeholder-gray-400
                               focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500
                               dark:bg-neutral-900 dark:text-white dark:border-neutral-700 dark:placeholder-gray-500"
                    >
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Precio
                    </label>
                    <input
                        type="number"
                        wire:model="precio"
                        placeholder="Ej: 5000"
                        class="w-full rounded-lg border bg-white text-gray-900 border-gray-300 placeholder-gray-400
                               focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500
                               dark:bg-neutral-900 dark:text-white dark:border-neutral-700 dark:placeholder-gray-500"
                    >
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Tipo de cancha
                    </label>
                    <select
                        wire:model="tipo"
                        class="w-full rounded-lg border bg-white text-gray-900 border-gray-300
                               focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500
                               dark:bg-neutral-900 dark:text-white dark:border-neutral-700"
                    >
                        <option value="">Seleccione tipo</option>
                        <option value="futbol">Fútbol</option>
                        <option value="padel">Pádel</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Cantidad de jugadores
                    </label>
                    <input
                        type="number"
                        wire:model="cantidad_jugadores"
                        placeholder="Ej: 10"
                        class="w-full rounded-lg border bg-white text-gray-900 border-gray-300 placeholder-gray-400
                               focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500
                               dark:bg-neutral-900 dark:text-white dark:border-neutral-700 dark:placeholder-gray-500"
                    >
                </div>

                <div class="col-span-full flex justify-end gap-3 pt-4">
                    <button
                        type="button"
                        wire:click="$set('showForm', false)"
                        class="px-4 py-2 rounded-lg bg-gray-200 text-gray-800 hover:bg-gray-300
                               dark:bg-neutral-700 dark:text-gray-200 dark:hover:bg-neutral-600 transition"
                    >
                        Cancelar
                    </button>

                    <button
                        type="submit"
                        class="px-4 py-2 rounded-lg bg-green-600 text-white hover:bg-green-700 transition shadow-sm"
                    >
                        Guardar cancha
                    </button>
                </div>

            </form>
        </div>
    @endif

    <!-- Confirmación -->
    @if ($showConfirmModal)
        <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
            <div class="bg-white dark:bg-neutral-800 rounded-xl p-6 shadow-lg w-full max-w-md">
                <p class="text-gray-900 dark:text-white mb-6 text-center">
                    ¿Está seguro de guardar la cancha?
                </p>

                <div class="flex justify-end gap-3">
                    <button
                        wire:click="$set('showConfirmModal', false)"
                        class="px-4 py-2 rounded-lg bg-gray-200 text-gray-800 hover:bg-gray-300
                               dark:bg-neutral-700 dark:text-gray-200 dark:hover:bg-neutral-600 transition"
                    >
                        Cancelar
                    </button>

                    <button
                        wire:click="guardarCancha"
                        class="px-4 py-2 rounded-lg bg-green-600 text-white hover:bg-green-700 transition shadow-sm"
                    >
                        Sí, guardar
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>
