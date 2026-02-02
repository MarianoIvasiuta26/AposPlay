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
    @if ($mostrarFormulario)
        <div class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto overflow-x-hidden p-4 backdrop-blur-sm bg-black/30">
            <!-- Modal Content -->
            <div class="relative w-full max-w-2xl rounded-xl bg-white p-6 shadow-2xl dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700">
                
                {{-- Mock Header --}}
                <div class="mb-5 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">
                        Configurar Horarios de Atención
                    </h3>
                    <button wire:click="toggle" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- Form Body --}}
                <div class="space-y-4 max-h-[60vh] overflow-y-auto pr-2">
                    @foreach ($dias as $dia)
                        <div class="grid grid-cols-12 gap-4 items-center border-b border-gray-100 dark:border-zinc-800 pb-3 last:border-0">
                            <!-- Label -->
                            <div class="col-span-3">
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                    {{ $dia->nombre }}
                                </span>
                            </div>

                            <!-- Inputs -->
                            <div class="col-span-9 grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs text-gray-500 mb-1">Apertura</label>
                                    <input
                                        type="time"
                                        wire:model.defer="horarios.{{ $dia->id }}.apertura"
                                        class="w-full rounded-lg border-gray-300 bg-gray-50 text-sm focus:border-green-500 focus:ring-green-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-gray-200"
                                    >
                                    @error("horarios.{$dia->id}.apertura") 
                                        <span class="text-xs text-red-500">{{ $message }}</span> 
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-xs text-gray-500 mb-1">Cierre</label>
                                    <input
                                        type="time"
                                        wire:model.defer="horarios.{{ $dia->id }}.cierre"
                                        class="w-full rounded-lg border-gray-300 bg-gray-50 text-sm focus:border-green-500 focus:ring-green-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-gray-200"
                                    >
                                    @error("horarios.{$dia->id}.cierre") 
                                        <span class="text-xs text-red-500">{{ $message }}</span> 
                                    @enderror
                                </div>
                            </div>
                        </div>
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
                        wire:click="guardar"
                        wire:loading.attr="disabled"
                        class="px-4 py-2 rounded-lg text-sm font-medium text-white bg-green-600 hover:bg-green-700 shadow-sm transition flex items-center gap-2"
                    >
                        <span wire:loading.remove wire:target="guardar">Guardar cambios</span>
                        <span wire:loading wire:target="guardar">Guardando...</span>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
