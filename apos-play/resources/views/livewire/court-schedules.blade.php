<div>
    {{-- Trigger Button --}}
    <button
        wire:click="toggle"
        class="text-sm font-medium text-green-600 hover:text-green-700
            dark:text-green-400 dark:hover:text-green-300 transition"
    >
        Definir horarios
    </button>

    {{-- Modal --}}
    @if ($showForm)
        <div class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto overflow-x-hidden p-4 backdrop-blur-sm bg-black/40">
            <!-- Modal Content -->
            <div class="relative w-full max-w-4xl rounded-xl bg-white p-6 shadow-2xl dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700">
                
                {{-- Header --}}
                <div class="mb-5 flex items-center justify-between border-b border-gray-100 dark:border-zinc-800 pb-3">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">
                            Configurar Horarios de Atención
                        </h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            Define los turnos de mañana y tarde para cada día.
                        </p>
                    </div>
                    <button wire:click="toggle" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- Form Body --}}
                <div class="space-y-2 max-h-[70vh] overflow-y-auto pr-2 custom-scrollbar">
                    {{-- Headers --}}
                    <div class="grid grid-cols-12 gap-4 text-xs font-semibold text-gray-500 dark:text-gray-400 mb-2 px-2 uppercase tracking-wider">
                        <div class="col-span-2">Día</div>
                        <div class="col-span-1 text-center">Estado</div>
                        <div class="col-span-9 pl-2 grid grid-cols-2 gap-4 text-center">
                            <div>Turno Mañana</div>
                            <div>Turno Tarde (Opcional)</div>
                        </div>
                    </div>

                    @foreach ($days as $day)
                        <div 
                            class="grid grid-cols-12 gap-4 items-center p-3 rounded-lg transition-colors border
                            {{ $schedules[$day->id]['active'] 
                                ? 'bg-gray-50 border-gray-200 dark:bg-zinc-800 dark:border-zinc-700 shadow-sm' 
                                : 'bg-transparent border-transparent opacity-60 hover:bg-gray-50 dark:hover:bg-zinc-800/30' 
                            }}"
                        >
                            <!-- Label -->
                            <div class="col-span-2">
                                <span class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                    {{ $day->nombre }}
                                </span>
                            </div>

                            <!-- Toggle Status -->
                            <div class="col-span-1 flex justify-center">
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" wire:model.live="schedules.{{ $day->id }}.active" class="sr-only peer">
                                    <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-green-300 dark:peer-focus:ring-green-800 rounded-full peer dark:bg-gray-600 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all dark:border-gray-600 peer-checked:bg-green-600"></div>
                                </label>
                            </div>

                            <!-- Inputs -->
                            <div class="col-span-9">
                                @if($schedules[$day->id]['active'])
                                    <div class="grid grid-cols-2 gap-6">
                                        <!-- Turno 1 -->
                                        <div class="flex gap-2 items-center">
                                            <input
                                                type="time"
                                                wire:model.defer="schedules.{{ $day->id }}.start_time_1"
                                                class="w-full rounded-md border-gray-300 bg-white text-sm focus:border-green-500 focus:ring-green-500 dark:border-zinc-600 dark:bg-zinc-900 dark:text-white p-1.5 shadow-sm"
                                                placeholder="Apertura"
                                            >
                                            <span class="text-gray-400 dark:text-gray-500">-</span>
                                            <input
                                                type="time"
                                                wire:model.defer="schedules.{{ $day->id }}.end_time_1"
                                                class="w-full rounded-md border-gray-300 bg-white text-sm focus:border-green-500 focus:ring-green-500 dark:border-zinc-600 dark:bg-zinc-900 dark:text-white p-1.5 shadow-sm"
                                                placeholder="Cierre"
                                            >
                                        </div>

                                        <!-- Turno 2 -->
                                        <div class="flex gap-2 items-center">
                                            <input
                                                type="time"
                                                wire:model.defer="schedules.{{ $day->id }}.start_time_2"
                                                class="w-full rounded-md border-gray-300 bg-white text-sm focus:border-green-500 focus:ring-green-500 dark:border-zinc-600 dark:bg-zinc-900 dark:text-white p-1.5 shadow-sm"
                                                placeholder="Apertura"
                                            >
                                            <span class="text-gray-400 dark:text-gray-500">-</span>
                                            <input
                                                type="time"
                                                wire:model.defer="schedules.{{ $day->id }}.end_time_2"
                                                class="w-full rounded-md border-gray-300 bg-white text-sm focus:border-green-500 focus:ring-green-500 dark:border-zinc-600 dark:bg-zinc-900 dark:text-white p-1.5 shadow-sm"
                                                placeholder="Cierre"
                                            >
                                        </div>
                                    </div>
                                @else
                                    <div class="text-center w-full">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-zinc-800 dark:text-gray-400">
                                            Cerrado
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </div>

                         @error("schedules.{$day->id}.*") 
                            <div class="text-xs text-red-500 text-right pr-2 -mt-1 mb-2">{{ $message }}</div> 
                        @enderror
                    @endforeach
                </div>

                {{-- Footer / Actions --}}
                <div class="mt-6 flex justify-end gap-3 pt-4 border-t border-gray-100 dark:border-zinc-800">
                    <button
                        wire:click="toggle"
                        class="px-4 py-2 rounded-lg text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 dark:bg-zinc-800 dark:text-gray-300 dark:hover:bg-zinc-700 transition"
                    >
                        Cancelar
                    </button>

                    <button
                        wire:click="save"
                        wire:loading.attr="disabled"
                        class="px-4 py-2 rounded-lg text-sm font-medium text-white bg-green-600 hover:bg-green-700 shadow-sm transition flex items-center gap-2"
                    >
                        <span wire:loading.remove wire:target="save">Guardar configuración</span>
                        <span wire:loading wire:target="save">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Guardando...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
