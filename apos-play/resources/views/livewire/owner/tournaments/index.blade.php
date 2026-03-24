<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Mis Torneos</h2>
        <a href="{{ route('owner.tournaments.create') }}"
            class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition-colors">
            <flux:icon name="plus" class="w-4 h-4" />
            Crear Torneo
        </a>
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

    @if($tournaments->isEmpty())
        <div class="rounded-xl bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-700 p-10 text-center">
            <p class="text-gray-500 dark:text-gray-400 text-lg mb-4">No tienes torneos creados.</p>
            <a href="{{ route('owner.tournaments.create') }}"
                class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700">
                Crear mi primer torneo
            </a>
        </div>
    @else
        <div class="rounded-xl bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-zinc-800">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-300">Nombre</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-300">Deporte</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-300">Formato</th>
                            <th class="px-4 py-3 text-center font-semibold text-gray-700 dark:text-gray-300">Equipos</th>
                            <th class="px-4 py-3 text-center font-semibold text-gray-700 dark:text-gray-300">Estado</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-300">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-zinc-800">
                        @foreach($tournaments as $tournament)
                            @php
                                $statusColors = [
                                    'draft'       => 'bg-zinc-100 text-zinc-700 dark:bg-zinc-600 dark:text-zinc-100',
                                    'open'        => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-700 dark:text-emerald-100',
                                    'in_progress' => 'bg-blue-100 text-blue-700 dark:bg-blue-700 dark:text-blue-100',
                                    'finished'    => 'bg-purple-100 text-purple-700 dark:bg-purple-700 dark:text-purple-100',
                                    'cancelled'   => 'bg-red-100 text-red-700 dark:bg-red-700 dark:text-red-100',
                                ];
                                $formatColors = [
                                    'round_robin'        => 'bg-blue-100 text-blue-700 dark:bg-blue-700 dark:text-blue-100',
                                    'single_elimination' => 'bg-amber-100 text-amber-700 dark:bg-amber-600 dark:text-amber-100',
                                ];
                            @endphp
                            <tr class="hover:bg-gray-50 dark:hover:bg-zinc-800/50">
                                <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ $tournament->name }}</td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ ucfirst($tournament->sport_type) }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $formatColors[$tournament->format->value] ?? '' }}">
                                        {{ $tournament->format->label() }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center text-gray-600 dark:text-gray-300">
                                    {{ $tournament->teams_count }} / {{ $tournament->max_teams }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$tournament->status->value] ?? '' }}">
                                        {{ $tournament->status->label() }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <a href="{{ route('tournaments.show', $tournament) }}"
                                            class="text-xs px-2 py-1 rounded border border-gray-300 dark:border-zinc-600 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-zinc-700">
                                            Ver
                                        </a>

                                        @if(in_array($tournament->status->value, ['draft', 'open']))
                                            <a href="{{ route('owner.tournaments.edit', $tournament) }}"
                                                class="text-xs px-2 py-1 rounded border border-blue-300 dark:border-blue-700 text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20">
                                                Editar
                                            </a>
                                        @endif

                                        <a href="{{ route('owner.tournaments.teams', $tournament) }}"
                                            class="text-xs px-2 py-1 rounded border border-gray-300 dark:border-zinc-600 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-zinc-700">
                                            Equipos
                                        </a>

                                        <a href="{{ route('owner.tournaments.fixture', $tournament) }}"
                                            class="text-xs px-2 py-1 rounded border border-gray-300 dark:border-zinc-600 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-zinc-700">
                                            Fixture
                                        </a>

                                        @if($tournament->status->value === 'draft')
                                            <button wire:click="openRegistration({{ $tournament->id }})"
                                                wire:confirm="¿Abrir inscripciones para este torneo?"
                                                class="text-xs px-2 py-1 rounded border border-green-300 dark:border-green-700 text-green-600 dark:text-green-400 hover:bg-green-50 dark:hover:bg-green-900/20">
                                                Abrir
                                            </button>
                                        @elseif($tournament->status->value === 'open')
                                            <button wire:click="startTournament({{ $tournament->id }})"
                                                wire:confirm="¿Iniciar el torneo ahora?"
                                                class="text-xs px-2 py-1 rounded border border-blue-300 dark:border-blue-700 text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20">
                                                Iniciar
                                            </button>
                                        @elseif($tournament->status->value === 'in_progress')
                                            <button wire:click="finishTournament({{ $tournament->id }})"
                                                wire:confirm="¿Finalizar el torneo?"
                                                class="text-xs px-2 py-1 rounded border border-purple-300 dark:border-purple-700 text-purple-600 dark:text-purple-400 hover:bg-purple-50 dark:hover:bg-purple-900/20">
                                                Finalizar
                                            </button>
                                        @endif

                                        @if(in_array($tournament->status->value, ['draft', 'cancelled']))
                                            <button wire:click="deleteTournament({{ $tournament->id }})"
                                                wire:confirm="¿Eliminar este torneo? Esta acción no se puede deshacer."
                                                class="text-xs px-2 py-1 rounded border border-red-300 dark:border-red-700 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20">
                                                Eliminar
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
