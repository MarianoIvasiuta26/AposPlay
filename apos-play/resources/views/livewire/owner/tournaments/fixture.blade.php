<div class="space-y-6">
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="{{ route('owner.tournaments.index') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">Mis Torneos</a>
                <span class="text-gray-400">/</span>
                <span class="text-sm text-gray-500 dark:text-gray-400">{{ $tournament->name }}</span>
                <span class="text-gray-400">/</span>
                <span class="text-sm text-gray-500 dark:text-gray-400">Fixture</span>
            </div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Fixture</h2>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            @if($tournament->status->value === 'in_progress')
                <flux:button wire:click="generateFixture" variant="filled" icon="arrow-path">
                    {{ $matchesByRound->isEmpty() ? 'Generar Fixture' : 'Regenerar Fixture' }}
                </flux:button>
                <flux:button wire:click="finishTournament"
                    wire:confirm="¿Finalizar el torneo? Esta acción no se puede deshacer."
                    variant="danger">
                    Finalizar Torneo
                </flux:button>
            @endif
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

    @if($tournament->status->value !== 'in_progress')
        <div class="p-4 rounded-lg bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 text-sm text-amber-800 dark:text-amber-300">
            El torneo debe estar <strong>En Curso</strong> para gestionar el fixture.
            Estado actual: <strong>{{ $tournament->status->label() }}</strong>
        </div>
    @endif

    @if($matchesByRound->isEmpty())
        <div class="rounded-xl bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-700 p-10 text-center">
            <p class="text-gray-500 dark:text-gray-400 text-lg mb-4">El fixture aún no ha sido generado.</p>
            @if($tournament->status->value === 'in_progress')
                <flux:button wire:click="generateFixture" variant="primary">
                    Generar Fixture
                </flux:button>
            @endif
        </div>
    @else
        <div class="space-y-6">
            @foreach($matchesByRound as $round => $matches)
                <div class="rounded-xl bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-700 overflow-hidden">
                    <div class="px-4 py-3 bg-gray-50 dark:bg-zinc-800 border-b border-gray-200 dark:border-zinc-700">
                        <h4 class="font-semibold text-gray-900 dark:text-white">
                            {{ $matches->first()->round_name ?? 'Ronda ' . $round }}
                        </h4>
                    </div>
                    <div class="divide-y divide-gray-100 dark:divide-zinc-800">
                        @foreach($matches as $match)
                            <div class="p-4 flex items-center gap-4">
                                <div class="flex-1 text-right">
                                    <span class="font-medium text-gray-900 dark:text-white">
                                        {{ $match->homeTeam?->name ?? 'BYE' }}
                                    </span>
                                </div>
                                <div class="shrink-0 text-center min-w-[120px]">
                                    @if($match->status->value === 'completed')
                                        <span class="text-xl font-bold text-gray-900 dark:text-white">
                                            {{ $match->home_score }} - {{ $match->away_score }}
                                        </span>
                                        <div class="mt-0.5">
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-emerald-100 text-emerald-700 dark:bg-emerald-700 dark:text-emerald-100">
                                                Completado
                                            </span>
                                        </div>
                                    @else
                                        <span class="text-sm text-gray-400">vs</span>
                                    @endif
                                </div>
                                <div class="flex-1 text-left">
                                    <span class="font-medium text-gray-900 dark:text-white">
                                        {{ $match->awayTeam?->name ?? 'BYE' }}
                                    </span>
                                </div>
                                <div class="shrink-0">
                                    @if($tournament->status->value === 'in_progress' && $match->homeTeam && $match->awayTeam)
                                        <button wire:click="openResultModal({{ $match->id }})"
                                            class="text-xs px-2 py-1 rounded border border-blue-300 dark:border-blue-700 text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20">
                                            {{ $match->status->value === 'completed' ? 'Editar' : 'Resultado' }}
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Result Modal --}}
    @if($showResultModal && $editingMatchId)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeResultModal"></div>

                <div class="relative inline-block align-bottom bg-white dark:bg-zinc-900 rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full border border-gray-200 dark:border-zinc-700">
                    <div class="px-6 pt-6 pb-4">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">
                            Registrar Resultado
                            @if($editingMatch)
                                — {{ $editingMatch->homeTeam?->name }} vs {{ $editingMatch->awayTeam?->name }}
                            @endif
                        </h3>

                        {{-- Score --}}
                        <div class="flex items-center gap-4 mb-6">
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    {{ $editingMatch?->homeTeam?->name ?? 'Local' }}
                                </label>
                                <input type="number" wire:model="homeScore" min="0"
                                    class="w-full rounded-lg border border-gray-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-gray-900 dark:text-gray-100 px-3 py-2 text-center text-lg font-bold focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div class="text-xl font-bold text-gray-400 pt-5">-</div>
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    {{ $editingMatch?->awayTeam?->name ?? 'Visitante' }}
                                </label>
                                <input type="number" wire:model="awayScore" min="0"
                                    class="w-full rounded-lg border border-gray-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-gray-900 dark:text-gray-100 px-3 py-2 text-center text-lg font-bold focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>

                        {{-- Player stats --}}
                        @if(!empty($playerStats))
                            <div>
                                <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Estadísticas de jugadores</h4>
                                <div class="overflow-x-auto max-h-64 overflow-y-auto">
                                    <table class="w-full text-xs">
                                        <thead class="bg-gray-50 dark:bg-zinc-800 sticky top-0">
                                            <tr>
                                                <th class="px-2 py-2 text-left text-gray-600 dark:text-gray-400">Jugador</th>
                                                <th class="px-2 py-2 text-left text-gray-600 dark:text-gray-400">Equipo</th>
                                                <th class="px-2 py-2 text-center text-gray-600 dark:text-gray-400">Goles</th>
                                                <th class="px-2 py-2 text-center text-gray-600 dark:text-gray-400">Asist.</th>
                                                <th class="px-2 py-2 text-center text-gray-600 dark:text-gray-400">Amarilla</th>
                                                <th class="px-2 py-2 text-center text-gray-600 dark:text-gray-400">Roja</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-100 dark:divide-zinc-800">
                                            @foreach($playerStats as $i => $stat)
                                                <tr>
                                                    <td class="px-2 py-1.5 text-gray-900 dark:text-white">{{ $stat['user_name'] }}</td>
                                                    <td class="px-2 py-1.5 text-gray-500 dark:text-gray-400">{{ $stat['team_name'] }}</td>
                                                    <td class="px-2 py-1.5 text-center">
                                                        <input type="number" wire:model="playerStats.{{ $i }}.goals" min="0"
                                                            class="w-12 rounded border border-gray-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-gray-900 dark:text-gray-100 px-1 py-0.5 text-center text-xs focus:outline-none focus:ring-1 focus:ring-blue-500">
                                                    </td>
                                                    <td class="px-2 py-1.5 text-center">
                                                        <input type="number" wire:model="playerStats.{{ $i }}.assists" min="0"
                                                            class="w-12 rounded border border-gray-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-gray-900 dark:text-gray-100 px-1 py-0.5 text-center text-xs focus:outline-none focus:ring-1 focus:ring-blue-500">
                                                    </td>
                                                    <td class="px-2 py-1.5 text-center">
                                                        <input type="number" wire:model="playerStats.{{ $i }}.yellow_cards" min="0"
                                                            class="w-12 rounded border border-gray-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-gray-900 dark:text-gray-100 px-1 py-0.5 text-center text-xs focus:outline-none focus:ring-1 focus:ring-blue-500">
                                                    </td>
                                                    <td class="px-2 py-1.5 text-center">
                                                        <input type="number" wire:model="playerStats.{{ $i }}.red_cards" min="0"
                                                            class="w-12 rounded border border-gray-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-gray-900 dark:text-gray-100 px-1 py-0.5 text-center text-xs focus:outline-none focus:ring-1 focus:ring-blue-500">
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="px-6 py-4 bg-gray-50 dark:bg-zinc-800 flex items-center justify-end gap-3">
                        <button wire:click="closeResultModal"
                            class="px-4 py-2 rounded-lg border border-gray-300 dark:border-zinc-600 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-zinc-700">
                            Cancelar
                        </button>
                        <flux:button wire:click="saveResult" variant="primary">
                            Guardar resultado
                        </flux:button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
