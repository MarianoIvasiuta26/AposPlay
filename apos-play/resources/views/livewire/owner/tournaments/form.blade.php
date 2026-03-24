<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="{{ route('owner.tournaments.index') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">Mis Torneos</a>
                <span class="text-gray-400">/</span>
                <span class="text-sm text-gray-500 dark:text-gray-400">
                    {{ $tournamentId ? 'Editar' : 'Crear' }}
                </span>
            </div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                {{ $tournamentId ? 'Editar Torneo' : 'Crear Torneo' }}
            </h2>
        </div>
    </div>

    @if($errorMessage)
        <div class="p-4 rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-sm text-red-800 dark:text-red-300">
            {{ $errorMessage }}
        </div>
    @endif

    @if($successMessage)
        <div class="p-4 rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-sm text-green-800 dark:text-green-300">
            {{ $successMessage }}
        </div>
    @endif

    <div class="rounded-xl bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-700 p-6 space-y-5">
        {{-- Name --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Nombre del torneo <span class="text-red-500">*</span>
            </label>
            <input type="text" wire:model="name"
                class="w-full rounded-lg border border-gray-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-gray-900 dark:text-gray-100 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            @error('name') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
        </div>

        {{-- Description --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Descripción</label>
            <textarea wire:model="description" rows="3"
                class="w-full rounded-lg border border-gray-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-gray-900 dark:text-gray-100 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
            @error('description') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            {{-- Sport type --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Deporte <span class="text-red-500">*</span>
                </label>
                <select wire:model="sport_type"
                    class="w-full rounded-lg border border-gray-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-gray-900 dark:text-gray-100 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="futbol">Fútbol</option>
                    <option value="padel">Pádel</option>
                    <option value="tenis">Tenis</option>
                    <option value="basquet">Básquet</option>
                    <option value="voley">Voley</option>
                </select>
                @error('sport_type') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
            </div>

            {{-- Format --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Formato <span class="text-red-500">*</span>
                </label>
                <select wire:model="format"
                    class="w-full rounded-lg border border-gray-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-gray-900 dark:text-gray-100 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="round_robin">Liga</option>
                    <option value="single_elimination">Eliminación Directa</option>
                </select>
                @error('format') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
            </div>
        </div>

        {{-- Court --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Cancha (opcional)</label>
            <select wire:model="court_id"
                class="w-full rounded-lg border border-gray-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-gray-900 dark:text-gray-100 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Sin cancha asignada</option>
                @foreach($courts as $court)
                    <option value="{{ $court->id }}">{{ $court->name }} ({{ $court->type }})</option>
                @endforeach
            </select>
            @error('court_id') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            {{-- Min teams --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Mín. equipos para iniciar <span class="text-red-500">*</span>
                </label>
                <input type="number" wire:model="min_teams" min="2" max="64"
                    class="w-full rounded-lg border border-gray-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-gray-900 dark:text-gray-100 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">Equipos con pago confirmado necesarios para poder iniciar.</p>
                @error('min_teams') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
            </div>

            {{-- Max teams --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Máx. equipos <span class="text-red-500">*</span>
                </label>
                <input type="number" wire:model="max_teams" min="2" max="64"
                    class="w-full rounded-lg border border-gray-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-gray-900 dark:text-gray-100 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('max_teams') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            {{-- Min players --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Mín. jugadores <span class="text-red-500">*</span>
                </label>
                <input type="number" wire:model="min_players" min="1" max="30"
                    class="w-full rounded-lg border border-gray-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-gray-900 dark:text-gray-100 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('min_players') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
            </div>

            {{-- Max players --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Máx. jugadores <span class="text-red-500">*</span>
                </label>
                <input type="number" wire:model="max_players" min="1" max="30"
                    class="w-full rounded-lg border border-gray-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-gray-900 dark:text-gray-100 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('max_players') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
            </div>
        </div>

        {{-- Entry fee --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Costo de inscripción (ARS) <span class="text-red-500">*</span>
            </label>
            <input type="number" wire:model="entry_fee" min="0" step="100"
                class="w-full rounded-lg border border-gray-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-gray-900 dark:text-gray-100 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            @error('entry_fee') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
        </div>

        {{-- Prize description --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Premio (descripción)</label>
            <input type="text" wire:model="prize_description"
                placeholder="Ej: Trofeo + $50.000 al ganador"
                class="w-full rounded-lg border border-gray-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-gray-900 dark:text-gray-100 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            @error('prize_description') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            {{-- Registration deadline --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Fecha límite de inscripción <span class="text-red-500">*</span>
                </label>
                <input type="datetime-local" wire:model="registration_deadline"
                    class="w-full rounded-lg border border-gray-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-gray-900 dark:text-gray-100 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('registration_deadline') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
            </div>

            {{-- Starts at --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Fecha de inicio <span class="text-red-500">*</span>
                </label>
                <input type="date" wire:model="starts_at"
                    class="w-full rounded-lg border border-gray-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-gray-900 dark:text-gray-100 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('starts_at') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
            </div>

            {{-- Ends at --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Fecha de fin</label>
                <input type="date" wire:model="ends_at"
                    class="w-full rounded-lg border border-gray-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-gray-900 dark:text-gray-100 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('ends_at') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
            </div>
        </div>
    </div>

    {{-- Actions --}}
    <div class="flex items-center gap-3 flex-wrap">
        <flux:button wire:click="save('draft')" variant="filled">
            Guardar como borrador
        </flux:button>
        <flux:button wire:click="save('open')" variant="primary">
            Guardar y abrir inscripciones
        </flux:button>
        <a href="{{ route('owner.tournaments.index') }}"
            class="inline-flex items-center px-4 py-2 rounded-lg border border-gray-300 dark:border-zinc-600 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-zinc-800">
            Cancelar
        </a>
    </div>
</div>
