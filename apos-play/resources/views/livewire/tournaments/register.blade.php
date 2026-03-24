<div class="max-w-2xl mx-auto space-y-6">
    {{-- Header --}}
    <div>
        <div class="flex items-center gap-2 mb-1">
            <a href="{{ route('tournaments.show', $tournament) }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">
                {{ $tournament->name }}
            </a>
            <span class="text-gray-400">/</span>
            <span class="text-sm text-gray-500 dark:text-gray-400">Inscribirse</span>
        </div>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Inscripción al Torneo</h2>
        <p class="text-gray-500 dark:text-gray-400 mt-1">{{ $tournament->name }}</p>
    </div>

    {{-- Alerts --}}
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

    @if(session('error'))
        <div class="p-4 rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-sm text-red-800 dark:text-red-300">
            {{ session('error') }}
        </div>
    @endif

    {{-- Step indicator --}}
    <div class="flex items-center gap-2">
        @foreach([1 => 'Crear Equipo', 2 => 'Miembros', 3 => 'Pago'] as $s => $label)
            <div class="flex items-center gap-1 {{ $loop->first ? '' : 'flex-1' }}">
                @if(!$loop->first)
                    <div class="flex-1 h-px bg-gray-200 dark:bg-zinc-700"></div>
                @endif
                <div class="flex items-center gap-1.5">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold
                        {{ $step >= $s ? 'bg-blue-600 text-white' : 'bg-gray-200 dark:bg-zinc-700 text-gray-500 dark:text-gray-400' }}">
                        {{ $s }}
                    </div>
                    <span class="text-xs font-medium hidden sm:inline
                        {{ $step >= $s ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400 dark:text-gray-500' }}">
                        {{ $label }}
                    </span>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Step 1: Create Team --}}
    @if($step === 1)
        <div class="rounded-xl bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-700 p-6 space-y-4">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Crear tu equipo</h3>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Nombre del equipo <span class="text-red-500">*</span>
                </label>
                <input type="text" wire:model="teamName"
                    placeholder="Ej: Los Tigres FC"
                    class="w-full rounded-lg border border-gray-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-gray-900 dark:text-gray-100 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('teamName')
                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="text-sm text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-zinc-800 rounded-lg p-3">
                <p>Serás el <strong>capitán</strong> del equipo. Después podrás agregar miembros.</p>
                <p class="mt-1">Jugadores: {{ $tournament->min_players }} mínimo — {{ $tournament->max_players }} máximo</p>
            </div>

            <flux:button wire:click="createTeam" variant="primary" class="w-full">
                Crear equipo
            </flux:button>
        </div>
    @endif

    {{-- Step 2: Add Members --}}
    @if($step === 2 && $team)
        <div class="rounded-xl bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-700 p-6 space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Equipo: {{ $team->name }}</h3>
                <span class="text-sm text-gray-500 dark:text-gray-400">
                    {{ $team->members->count() }} / {{ $tournament->max_players }} jugadores
                </span>
            </div>

            {{-- Members list --}}
            <div class="space-y-2">
                @foreach($team->members as $member)
                    <div class="flex items-center justify-between p-3 rounded-lg bg-gray-50 dark:bg-zinc-800">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-700 dark:text-blue-300 text-sm font-bold">
                                {{ substr($member->user->name ?? 'J', 0, 1) }}
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $member->user?->name ?? 'Jugador' }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $member->user?->email ?? '' }}</p>
                            </div>
                        </div>
                        @if($member->is_captain)
                            <span class="text-xs font-medium text-yellow-600 dark:text-yellow-400">Capitán</span>
                        @else
                            <button wire:click="removeMember({{ $member->user_id }})"
                                class="text-xs text-red-600 dark:text-red-400 hover:underline">
                                Remover
                            </button>
                        @endif
                    </div>
                @endforeach
            </div>

            {{-- Add member search --}}
            @if($team->members->count() < $tournament->max_players)
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Agregar jugador por email</label>
                    <div class="flex gap-2">
                        <input type="email" wire:model.live="searchEmail"
                            placeholder="email@ejemplo.com"
                            class="flex-1 rounded-lg border border-gray-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-gray-900 dark:text-gray-100 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <flux:button wire:click="searchUsers" size="sm">Buscar</flux:button>
                    </div>

                    @if(!empty($searchResults))
                        <div class="border border-gray-200 dark:border-zinc-700 rounded-lg divide-y divide-gray-100 dark:divide-zinc-800">
                            @foreach($searchResults as $result)
                                <div class="flex items-center justify-between p-3">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $result['name'] }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $result['email'] }}</p>
                                    </div>
                                    <button wire:click="addMember({{ $result['id'] }})"
                                        class="text-sm text-blue-600 dark:text-blue-400 font-medium hover:underline">
                                        Agregar
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    @elseif(strlen($searchEmail) >= 3)
                        <p class="text-sm text-gray-500 dark:text-gray-400 italic">No se encontraron usuarios disponibles.</p>
                    @endif
                </div>
            @endif

            <div class="flex gap-3 pt-2 border-t border-gray-100 dark:border-zinc-800">
                @if((float)$tournament->entry_fee > 0)
                    <flux:button wire:click="goToPayment" variant="primary" class="flex-1">
                        Continuar al pago
                    </flux:button>
                @else
                    <div class="flex-1 text-center">
                        <p class="text-sm text-green-600 dark:text-green-400 font-medium">Torneo gratuito. Tu equipo ya está inscripto.</p>
                        <a href="{{ route('tournaments.show', $tournament) }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">Ver torneo</a>
                    </div>
                @endif
            </div>
        </div>
    @endif

    {{-- Step 3: Payment --}}
    @if($step === 3 && $team)
        <div class="rounded-xl bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-700 p-6 space-y-4">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Pago de inscripción</h3>

            <div class="rounded-lg bg-gray-50 dark:bg-zinc-800 p-4 space-y-2">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600 dark:text-gray-300">Torneo:</span>
                    <span class="font-medium text-gray-900 dark:text-white">{{ $tournament->name }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600 dark:text-gray-300">Equipo:</span>
                    <span class="font-medium text-gray-900 dark:text-white">{{ $team->name }}</span>
                </div>
                <div class="flex justify-between text-sm border-t border-gray-200 dark:border-zinc-700 pt-2 mt-2">
                    <span class="font-semibold text-gray-900 dark:text-white">Total a pagar:</span>
                    <span class="font-bold text-lg text-gray-900 dark:text-white">${{ number_format($tournament->entry_fee, 0, ',', '.') }}</span>
                </div>
            </div>

            @php
                $payBadge = [
                    'pending'  => 'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-300',
                    'paid'     => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
                    'refunded' => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
                ];
            @endphp
            <div class="flex items-center gap-2">
                <span class="text-sm text-gray-600 dark:text-gray-300">Estado del pago:</span>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $payBadge[$team->payment_status->value] ?? '' }}">
                    {{ $team->payment_status->label() }}
                </span>
            </div>

            @if($team->payment_status->value === 'paid')
                <div class="p-4 rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-sm text-green-800 dark:text-green-300">
                    ¡Tu inscripción está pagada! Tu equipo está confirmado en el torneo.
                </div>
                <a href="{{ route('tournaments.show', $tournament) }}"
                    class="block text-center text-sm text-blue-600 dark:text-blue-400 hover:underline">
                    Ver torneo
                </a>
            @else
                <flux:button wire:click="pay" variant="primary" class="w-full" icon="credit-card">
                    Pagar con MercadoPago
                </flux:button>
            @endif
        </div>
    @endif
</div>
