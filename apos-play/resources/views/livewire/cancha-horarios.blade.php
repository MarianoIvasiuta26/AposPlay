<div>
    {{-- Stop trying to control. --}}
    <div class="mt-4">

        <button
            wire:click="toggle"
            class="text-sm font-medium text-green-600 hover:text-green-700
                dark:text-green-400 dark:hover:text-green-300 transition"
        >
            Definir horarios
        </button>

        @if ($mostrarFormulario)
            <div
                class="mt-4 rounded-xl border p-5
                    bg-neutral-50 border-neutral-200
                    dark:bg-neutral-900 dark:border-neutral-700"
            >
                <h4 class="text-md font-semibold mb-4 text-gray-900 dark:text-neutral-100">
                    Horarios de atención
                </h4>

                <div class="space-y-4">
                    @foreach ($dias as $dia)
                        <div class="grid grid-cols-3 gap-3 items-center">
                            <span class="text-sm font-medium text-gray-700 dark:text-neutral-300">
                                {{ $dia->nombre }}
                            </span>

                            <input
                                type="time"
                                wire:model.defer="horarios.{{ $dia->id }}.apertura"
                                class="rounded-lg border px-2 py-1
                                    bg-white border-neutral-300
                                    dark:bg-neutral-800 dark:border-neutral-700"
                            >

                            <input
                                type="time"
                                wire:model.defer="horarios.{{ $dia->id }}.cierre"
                                class="rounded-lg border px-2 py-1
                                    bg-white border-neutral-300
                                    dark:bg-neutral-800 dark:border-neutral-700"
                            >
                        </div>
                    @endforeach
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <button
                        wire:click="toggle"
                        class="px-4 py-2 rounded-lg
                            bg-neutral-200 text-neutral-800
                            dark:bg-neutral-700 dark:text-neutral-200"
                    >
                        Cancelar
                    </button>

                    <button
                        wire:click="guardar"
                        class="px-4 py-2 rounded-lg
                            bg-green-600 hover:bg-green-700
                            text-white shadow-sm"
                    >
                        Guardar horarios
                    </button>
                </div>
            </div>
        @endif
    </div>

</div>
