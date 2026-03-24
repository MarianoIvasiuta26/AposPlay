<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Torneos</h2>
        <div class="flex items-center gap-3">
            <select wire:model.live="filterSport"
                class="rounded-lg border border-gray-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-gray-900 dark:text-gray-100 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Todos los deportes</option>
                @foreach($sports as $sport)
                    <option value="{{ $sport }}">{{ ucfirst($sport) }}</option>
                @endforeach
            </select>
        </div>
    </div>

    @if($tournaments->isEmpty())
        <div class="rounded-xl bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-700 shadow p-10 text-center">
            <div class="text-gray-400 dark:text-gray-500 text-5xl mb-4">🏆</div>
            <p class="text-gray-500 dark:text-gray-400 text-lg">No hay torneos disponibles en este momento.</p>
        </div>
    @else
        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            @foreach($tournaments as $tournament)
                @php
                    $formatColors = [
                        'round_robin'        => 'bg-blue-100 text-blue-700 dark:bg-blue-700 dark:text-blue-100',
                        'single_elimination' => 'bg-amber-100 text-amber-700 dark:bg-amber-600 dark:text-amber-100',
                    ];
                    $statusColors = [
                        'open'        => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-700 dark:text-emerald-100',
                        'in_progress' => 'bg-blue-100 text-blue-700 dark:bg-blue-700 dark:text-blue-100',
                    ];
                @endphp
                <div class="rounded-xl bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-700 shadow-sm hover:shadow-md transition-shadow">
                    <div class="p-6 space-y-4">
                        <div class="flex items-start justify-between gap-2">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white leading-tight">{{ $tournament->name }}</h3>
                            <span class="inline-flex shrink-0 items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$tournament->status->value] ?? 'bg-gray-100 text-gray-700' }}">
                                {{ $tournament->status->label() }}
                            </span>
                        </div>

                        <div class="flex flex-wrap gap-2">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $formatColors[$tournament->format->value] ?? '' }}">
                                {{ $tournament->format->label() }}
                            </span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-zinc-100 text-zinc-700 dark:bg-zinc-600 dark:text-zinc-100">
                                {{ ucfirst($tournament->sport_type) }}
                            </span>
                        </div>

                        <div class="space-y-1.5 text-sm text-gray-600 dark:text-gray-300">
                            <div class="flex items-center gap-2">
                                <flux:icon name="users" class="w-4 h-4 shrink-0" />
                                <span>{{ $tournament->teams_count }} / {{ $tournament->max_teams }} equipos</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <flux:icon name="calendar-days" class="w-4 h-4 shrink-0" />
                                <span>Inscripciones hasta {{ $tournament->registration_deadline->format('d/m/Y H:i') }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <flux:icon name="banknotes" class="w-4 h-4 shrink-0" />
                                @if((float)$tournament->entry_fee > 0)
                                    <span>Inscripción: ${{ number_format($tournament->entry_fee, 0, ',', '.') }}</span>
                                @else
                                    <span>Gratuito</span>
                                @endif
                            </div>
                        </div>

                        <div class="pt-3 border-t border-gray-100 dark:border-zinc-800 flex gap-2">
                            <a href="{{ route('tournaments.show', $tournament) }}"
                                class="flex-1 inline-flex justify-center items-center px-3 py-2 rounded-lg border border-gray-300 dark:border-zinc-600 text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-zinc-800 transition-colors">
                                Ver detalles
                            </a>
                            @auth
                                @if($tournament->isRegistrationOpen())
                                    <a href="{{ route('tournaments.register', $tournament) }}"
                                        class="flex-1 inline-flex justify-center items-center px-3 py-2 rounded-lg bg-blue-600 text-sm font-medium text-white hover:bg-blue-700 transition-colors">
                                        Inscribirse
                                    </a>
                                @endif
                            @endauth
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
