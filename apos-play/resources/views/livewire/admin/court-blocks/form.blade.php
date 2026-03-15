<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
            {{ __('Nuevo Bloqueo de Horario') }}
        </h2>
    </div>

    <div class="bg-white dark:bg-zinc-900 overflow-hidden shadow-sm sm:rounded-lg border border-gray-100 dark:border-zinc-800 p-6">
        <form wire:submit="save" class="space-y-4">
            <div>
                <label for="court_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Cancha</label>
                <select wire:model="court_id" id="court_id"
                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-zinc-600 dark:bg-zinc-800 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Seleccionar cancha</option>
                    @foreach($courts as $court)
                        <option value="{{ $court->id }}">{{ $court->name }}</option>
                    @endforeach
                </select>
                @error('court_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Fecha inicio</label>
                    <input type="date" wire:model="start_date" id="start_date"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-zinc-600 dark:bg-zinc-800 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('start_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Fecha fin</label>
                    <input type="date" wire:model="end_date" id="end_date"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-zinc-600 dark:bg-zinc-800 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('end_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" wire:model.live="fullDay"
                        class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500 dark:border-zinc-600 dark:bg-zinc-800">
                    <span class="text-sm text-gray-700 dark:text-gray-300">Dia completo</span>
                </label>
            </div>

            @if(!$fullDay)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="start_time" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Hora inicio</label>
                        <input type="time" wire:model="start_time" id="start_time"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-zinc-600 dark:bg-zinc-800 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @error('start_time') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="end_time" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Hora fin</label>
                        <input type="time" wire:model="end_time" id="end_time"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-zinc-600 dark:bg-zinc-800 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @error('end_time') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            @endif

            <div>
                <label for="reason" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Motivo</label>
                <input type="text" wire:model="reason" id="reason" placeholder="Ej: Mantenimiento, evento privado..."
                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-zinc-600 dark:bg-zinc-800 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                @error('reason') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex gap-3 pt-4">
                <button type="submit"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 cursor-pointer">
                    Crear Bloqueo
                </button>
                <a href="{{ route('admin.court-blocks') }}" wire:navigate
                    class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-zinc-700 border border-transparent rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest hover:bg-gray-300 dark:hover:bg-zinc-600 cursor-pointer">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
