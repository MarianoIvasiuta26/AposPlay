<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="{{ route('tournaments.index') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">Torneos</a>
                <span class="text-gray-400">/</span>
                <span class="text-sm text-gray-500 dark:text-gray-400">{{ $tournament->name }}</span>
            </div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $tournament->name }}</h2>
            @if($tournament->description)
                <p class="mt-1 text-gray-500 dark:text-gray-400 text-sm">{{ $tournament->description }}</p>
            @endif
        </div>
        @php
            $statusColors = [
                'draft'       => 'bg-zinc-100 text-zinc-700 dark:bg-zinc-600 dark:text-zinc-100',
                'open'        => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-700 dark:text-emerald-100',
                'in_progress' => 'bg-blue-100 text-blue-700 dark:bg-blue-700 dark:text-blue-100',
                'finished'    => 'bg-purple-100 text-purple-700 dark:bg-purple-700 dark:text-purple-100',
                'cancelled'   => 'bg-red-100 text-red-700 dark:bg-red-700 dark:text-red-100',
            ];
        @endphp
        <span class="inline-flex shrink-0 items-center px-3 py-1 rounded-full text-sm font-medium {{ $statusColors[$tournament->status->value] ?? '' }}">
            {{ $tournament->status->label() }}
        </span>
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

    {{-- Info cards --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="rounded-xl bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-700 p-4">
            <div class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">Deporte</div>
            <div class="text-lg font-semibold text-gray-900 dark:text-white">{{ ucfirst($tournament->sport_type) }}</div>
        </div>
        <div class="rounded-xl bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-700 p-4">
            <div class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">Formato</div>
            <div class="text-lg font-semibold text-gray-900 dark:text-white">{{ $tournament->format->label() }}</div>
        </div>
        <div class="rounded-xl bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-700 p-4">
            <div class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">Equipos</div>
            <div class="text-lg font-semibold text-gray-900 dark:text-white">{{ $tournament->teams->count() }} / {{ $tournament->max_teams }}</div>
        </div>
        <div class="rounded-xl bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-700 p-4">
            <div class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">Inicio</div>
            <div class="text-lg font-semibold text-gray-900 dark:text-white">{{ $tournament->starts_at->format('d/m/Y') }}</div>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="border-b border-gray-200 dark:border-zinc-700">
        <nav class="flex gap-1 -mb-px overflow-x-auto">
            @foreach(['fixture' => 'Fixture', 'standings' => 'Tabla', 'stats' => 'Estadísticas', 'teams' => 'Equipos'] as $tab => $label)
                <button wire:click="setTab('{{ $tab }}')"
                    class="px-4 py-2.5 text-sm font-medium whitespace-nowrap border-b-2 transition-colors
                    {{ $activeTab === $tab
                        ? 'border-blue-600 text-blue-600 dark:text-blue-400 dark:border-blue-400'
                        : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200' }}">
                    {{ $label }}
                </button>
            @endforeach
        </nav>
    </div>

    {{-- Tab content --}}
    <div>
        {{-- Fixture Tab --}}
        @if($activeTab === 'fixture')
            @if($matchesByRound->isEmpty())
                <div class="rounded-xl bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-700 p-8 text-center text-gray-500 dark:text-gray-400">
                    El fixture aún no ha sido generado.
                </div>
            @else
                <div class="space-y-6">
                    @foreach($matchesByRound as $round => $matches)
                        <div class="rounded-xl bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-700 overflow-hidden">
                            <div class="px-4 py-3 bg-gray-50 dark:bg-zinc-800 border-b border-gray-200 dark:border-zinc-700">
                                <h4 class="font-semibold text-gray-900 dark:text-white">{{ $matches->first()->round_name ?? 'Ronda ' . $round }}</h4>
                            </div>
                            <div class="divide-y divide-gray-100 dark:divide-zinc-800">
                                @foreach($matches as $match)
                                    <div class="p-4 flex items-center justify-between gap-4">
                                        <div class="flex-1 text-right">
                                            <span class="font-medium text-gray-900 dark:text-white">{{ $match->homeTeam?->name ?? 'BYE' }}</span>
                                        </div>
                                        <div class="shrink-0 text-center min-w-[80px]">
                                            @if($match->status->value === 'completed')
                                                <span class="text-lg font-bold text-gray-900 dark:text-white">
                                                    {{ $match->home_score }} - {{ $match->away_score }}
                                                </span>
                                            @else
                                                <span class="text-sm text-gray-400 dark:text-gray-500">vs</span>
                                            @endif
                                        </div>
                                        <div class="flex-1 text-left">
                                            <span class="font-medium text-gray-900 dark:text-white">{{ $match->awayTeam?->name ?? 'BYE' }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        @endif

        {{-- Standings Tab --}}
        @if($activeTab === 'standings')
            @if($tournament->format->value !== 'round_robin')
                <div class="rounded-xl bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-700 p-8 text-center text-gray-500 dark:text-gray-400">
                    La tabla de posiciones solo está disponible para torneos en formato Liga.
                </div>
            @elseif(!$standings || $standings->isEmpty())
                <div class="rounded-xl bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-700 p-8 text-center text-gray-500 dark:text-gray-400">
                    No hay datos de posiciones aún.
                </div>
            @else
                <div class="rounded-xl bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-700 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 dark:bg-zinc-800">
                                <tr>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-300">#</th>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-300">Equipo</th>
                                    <th class="px-4 py-3 text-center font-semibold text-gray-700 dark:text-gray-300">PJ</th>
                                    <th class="px-4 py-3 text-center font-semibold text-gray-700 dark:text-gray-300">G</th>
                                    <th class="px-4 py-3 text-center font-semibold text-gray-700 dark:text-gray-300">E</th>
                                    <th class="px-4 py-3 text-center font-semibold text-gray-700 dark:text-gray-300">P</th>
                                    <th class="px-4 py-3 text-center font-semibold text-gray-700 dark:text-gray-300">GF</th>
                                    <th class="px-4 py-3 text-center font-semibold text-gray-700 dark:text-gray-300">GC</th>
                                    <th class="px-4 py-3 text-center font-semibold text-gray-700 dark:text-gray-300">Dif</th>
                                    <th class="px-4 py-3 text-center font-semibold text-gray-700 dark:text-gray-300">Pts</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-zinc-800">
                                @foreach($standings as $i => $row)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-zinc-800/50">
                                        <td class="px-4 py-3 text-gray-500 dark:text-gray-400">{{ $i + 1 }}</td>
                                        <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ $row['team']->name }}</td>
                                        <td class="px-4 py-3 text-center text-gray-600 dark:text-gray-300">{{ $row['played'] }}</td>
                                        <td class="px-4 py-3 text-center text-gray-600 dark:text-gray-300">{{ $row['won'] }}</td>
                                        <td class="px-4 py-3 text-center text-gray-600 dark:text-gray-300">{{ $row['drawn'] }}</td>
                                        <td class="px-4 py-3 text-center text-gray-600 dark:text-gray-300">{{ $row['lost'] }}</td>
                                        <td class="px-4 py-3 text-center text-gray-600 dark:text-gray-300">{{ $row['gf'] }}</td>
                                        <td class="px-4 py-3 text-center text-gray-600 dark:text-gray-300">{{ $row['ga'] }}</td>
                                        <td class="px-4 py-3 text-center {{ $row['gd'] > 0 ? 'text-green-600 dark:text-green-400' : ($row['gd'] < 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-600 dark:text-gray-300') }}">
                                            {{ $row['gd'] > 0 ? '+' : '' }}{{ $row['gd'] }}
                                        </td>
                                        <td class="px-4 py-3 text-center font-bold text-gray-900 dark:text-white">{{ $row['points'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        @endif

        {{-- Stats Tab --}}
        @if($activeTab === 'stats')
            @if($playerStats->isEmpty())
                <div class="rounded-xl bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-700 p-8 text-center text-gray-500 dark:text-gray-400">
                    No hay estadísticas de jugadores aún.
                </div>
            @else
                <div class="rounded-xl bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-700 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 dark:bg-zinc-800">
                                <tr>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-300">Jugador</th>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-300">Equipo</th>
                                    <th class="px-4 py-3 text-center font-semibold text-gray-700 dark:text-gray-300">Goles</th>
                                    <th class="px-4 py-3 text-center font-semibold text-gray-700 dark:text-gray-300">Asistencias</th>
                                    <th class="px-4 py-3 text-center font-semibold text-gray-700 dark:text-gray-300">Amarillas</th>
                                    <th class="px-4 py-3 text-center font-semibold text-gray-700 dark:text-gray-300">Rojas</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-zinc-800">
                                @foreach($playerStats as $stat)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-zinc-800/50">
                                        <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ $stat['user']?->name ?? 'Jugador' }}</td>
                                        <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $stat['team']?->name ?? '-' }}</td>
                                        <td class="px-4 py-3 text-center font-semibold text-gray-900 dark:text-white">{{ $stat['goals'] }}</td>
                                        <td class="px-4 py-3 text-center text-gray-600 dark:text-gray-300">{{ $stat['assists'] }}</td>
                                        <td class="px-4 py-3 text-center">
                                            @if($stat['yellow_cards'] > 0)
                                                <span class="inline-block w-3 h-4 bg-yellow-400 rounded-sm align-middle"></span>
                                                <span class="text-gray-600 dark:text-gray-300">{{ $stat['yellow_cards'] }}</span>
                                            @else
                                                <span class="text-gray-400">0</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            @if($stat['red_cards'] > 0)
                                                <span class="inline-block w-3 h-4 bg-red-500 rounded-sm align-middle"></span>
                                                <span class="text-gray-600 dark:text-gray-300">{{ $stat['red_cards'] }}</span>
                                            @else
                                                <span class="text-gray-400">0</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        @endif

        {{-- Teams Tab --}}
        @if($activeTab === 'teams')
            @if($tournament->teams->isEmpty())
                <div class="rounded-xl bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-700 p-8 text-center text-gray-500 dark:text-gray-400">
                    Aún no hay equipos inscriptos.
                    @if($tournament->isRegistrationOpen())
                        <div class="mt-4">
                            <a href="{{ route('tournaments.register', $tournament) }}"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                                Inscribir mi equipo
                            </a>
                        </div>
                    @endif
                </div>
            @else
                <div class="grid gap-4 md:grid-cols-2">
                    @foreach($tournament->teams as $team)
                        <div class="rounded-xl bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-700 p-4">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="font-semibold text-gray-900 dark:text-white">{{ $team->name }}</h4>
                                @php
                                    $payBadge = [
                                        'pending'  => 'bg-amber-100 text-amber-700 dark:bg-amber-600 dark:text-amber-100',
                                        'paid'     => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-700 dark:text-emerald-100',
                                        'refunded' => 'bg-zinc-100 text-zinc-700 dark:bg-zinc-600 dark:text-zinc-100',
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $payBadge[$team->payment_status->value] ?? '' }}">
                                    {{ $team->payment_status->label() }}
                                </span>
                            </div>
                            <div class="text-sm text-gray-500 dark:text-gray-400 mb-2">Capitán: {{ $team->captain?->name ?? '-' }}</div>
                            @if($team->members->isNotEmpty())
                                <div class="flex flex-wrap gap-1">
                                    @foreach($team->members as $member)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-zinc-100 text-zinc-700 dark:bg-zinc-600 dark:text-zinc-100">
                                            {{ $member->user?->name ?? 'Jugador' }}
                                            @if($member->is_captain)
                                                <span class="ml-1 text-yellow-500">★</span>
                                            @endif
                                        </span>
                                    @endforeach
                                </div>
                            @endif
                            @auth
                                @if($team->captain_id === auth()->id() && in_array($tournament->status->value, ['open', 'draft']))
                                    @php
                                        $startsAt = \Carbon\Carbon::parse($tournament->starts_at, 'America/Argentina/Buenos_Aires')->startOfDay();
                                        $hoursLeft = now('America/Argentina/Buenos_Aires')->diffInHours($startsAt, false);
                                        $withRefund = $hoursLeft >= 36;
                                        $confirmMsg = $withRefund
                                            ? "¿Dar de baja a tu equipo '{$team->name}'? Como faltan más de 36hs, se te devolverá el importe de inscripción."
                                            : "¿Dar de baja a tu equipo '{$team->name}'? Faltan menos de 36hs para el inicio — NO se realizará reembolso.";
                                    @endphp
                                    <div class="mt-3 pt-3 border-t border-gray-100 dark:border-zinc-700 space-y-1">
                                        <button wire:click="withdrawMyTeam"
                                            wire:confirm="{{ $confirmMsg }}"
                                            class="text-xs px-3 py-1.5 rounded border border-red-300 dark:border-red-700 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20">
                                            Dar de baja mi equipo
                                        </button>
                                        @if(!$withRefund && $team->payment_status->value === 'paid')
                                            <p class="text-xs text-amber-600 dark:text-amber-400 italic">
                                                Faltan menos de 36hs — si te das de baja no se reembolsará la inscripción.
                                            </p>
                                        @endif
                                    </div>
                                @endif
                            @endauth
                        </div>
                    @endforeach
                </div>
            @endif
        @endif
    </div>

    {{-- Join CTA --}}
    @auth
        @if($tournament->isRegistrationOpen())
            <div class="rounded-xl bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 p-6 flex items-center justify-between gap-4">
                <div>
                    <h3 class="font-semibold text-blue-900 dark:text-blue-200">¡Inscribite en este torneo!</h3>
                    <p class="text-sm text-blue-700 dark:text-blue-300 mt-0.5">Quedan {{ $tournament->max_teams - $tournament->teams->count() }} lugares disponibles.</p>
                </div>
                <a href="{{ route('tournaments.register', $tournament) }}"
                    class="shrink-0 inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition-colors">
                    Inscribirse
                </a>
            </div>
        @endif
    @endauth
</div>
