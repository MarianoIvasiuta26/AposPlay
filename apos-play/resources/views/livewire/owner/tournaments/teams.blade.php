<div class="space-y-6">
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="{{ route('owner.tournaments.index') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">Mis Torneos</a>
                <span class="text-gray-400">/</span>
                <span class="text-sm text-gray-500 dark:text-gray-400">{{ $tournament->name }}</span>
                <span class="text-gray-400">/</span>
                <span class="text-sm text-gray-500 dark:text-gray-400">Equipos</span>
            </div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Equipos Inscriptos</h2>
        </div>
        <div class="rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 px-4 py-2">
            <span class="text-sm text-gray-600 dark:text-gray-300">Total recaudado: </span>
            <span class="font-bold text-green-700 dark:text-green-400">${{ number_format($totalCollected, 0, ',', '.') }}</span>
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

    {{-- Progress & Status Panel --}}
    <div class="rounded-xl bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-700 p-5 space-y-4">
        <div class="flex flex-wrap items-center justify-between gap-4">
            {{-- Progress toward min_teams --}}
            <div class="flex-1 min-w-[220px]">
                <div class="flex items-center justify-between mb-1">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                        Equipos confirmados
                    </span>
                    <span class="text-sm font-bold {{ $paidTeamsCount >= $tournament->min_teams ? 'text-emerald-600 dark:text-emerald-400' : 'text-amber-600 dark:text-amber-400' }}">
                        {{ $paidTeamsCount }} / {{ $tournament->min_teams }} mínimo
                    </span>
                </div>
                <div class="w-full bg-gray-200 dark:bg-zinc-700 rounded-full h-2.5">
                    @php $progress = $tournament->min_teams > 0 ? min(100, ($paidTeamsCount / $tournament->min_teams) * 100) : 100; @endphp
                    <div class="h-2.5 rounded-full transition-all duration-300 {{ $paidTeamsCount >= $tournament->min_teams ? 'bg-emerald-500' : 'bg-amber-400' }}"
                        style="width: {{ $progress }}%"></div>
                </div>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                    Máximo: {{ $tournament->max_teams }} equipos
                </p>
            </div>

            {{-- Status Actions --}}
            <div class="flex flex-wrap gap-2">
                @if($tournament->status->value === 'draft')
                    <button wire:click="openRegistration"
                        class="px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold">
                        Abrir inscripciones
                    </button>
                @endif

                @if($tournament->status->value === 'open')
                    @if($paidTeamsCount >= $tournament->min_teams)
                        <button wire:click="startTournament"
                            wire:confirm="¿Iniciar el torneo? Ya no se podrán inscribir nuevos equipos."
                            class="px-4 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold">
                            Iniciar torneo
                        </button>
                    @else
                        <div class="px-4 py-2 rounded-lg bg-gray-100 dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 text-sm text-gray-500 dark:text-gray-400">
                            Faltan {{ $tournament->min_teams - $paidTeamsCount }} equipo(s) confirmados para iniciar
                        </div>
                    @endif
                @endif

                @if($tournament->status->value === 'in_progress')
                    <a href="{{ route('owner.tournaments.fixture', $tournament) }}"
                        class="px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold inline-flex items-center gap-1">
                        Ver fixture
                    </a>
                    <button wire:click="finishTournament"
                        wire:confirm="¿Finalizar el torneo?"
                        class="px-4 py-2 rounded-lg border border-gray-300 dark:border-zinc-600 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-zinc-800">
                        Finalizar torneo
                    </button>
                @endif
            </div>
        </div>
    </div>

    @if($teams->isEmpty())
        <div class="rounded-xl bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-700 p-10 text-center text-gray-500 dark:text-gray-400">
            No hay equipos inscriptos en este torneo aún.
        </div>
    @else
        <div class="rounded-xl bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-zinc-800">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-300">Equipo</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-300">Capitán</th>
                            <th class="px-4 py-3 text-center font-semibold text-gray-700 dark:text-gray-300">Jugadores</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-300">Miembros</th>
                            <th class="px-4 py-3 text-center font-semibold text-gray-700 dark:text-gray-300">Pago</th>
                            <th class="px-4 py-3 text-center font-semibold text-gray-700 dark:text-gray-300">Monto</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-300">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-zinc-800">
                        @foreach($teams as $team)
                            @php
                                $payBadge = [
                                    'pending'  => 'bg-amber-100 text-amber-700 dark:bg-amber-600 dark:text-amber-100',
                                    'paid'     => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-700 dark:text-emerald-100',
                                    'refunded' => 'bg-zinc-100 text-zinc-700 dark:bg-zinc-600 dark:text-zinc-100',
                                ];
                                $canManage = in_array($tournament->status->value, ['draft', 'open']);
                                $startsAt = \Carbon\Carbon::parse($tournament->starts_at, 'America/Argentina/Buenos_Aires')->startOfDay();
                                $hoursUntilStart = now('America/Argentina/Buenos_Aires')->diffInHours($startsAt, false);
                                $eligibleForRefund = $hoursUntilStart >= 36;
                                $withdrawConfirm = $eligibleForRefund
                                    ? "¿Dar de baja al equipo {$team->name}? Si pagó, recibirá un reembolso."
                                    : "¿Dar de baja al equipo {$team->name}? Faltan menos de 36hs para el inicio — NO se realizará reembolso.";
                            @endphp
                            <tr class="hover:bg-gray-50 dark:hover:bg-zinc-800/50">
                                <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ $team->name }}</td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $team->captain?->name ?? '-' }}</td>
                                <td class="px-4 py-3 text-center text-gray-600 dark:text-gray-300">{{ $team->members_count }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($team->members as $member)
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs bg-zinc-100 text-zinc-700 dark:bg-zinc-600 dark:text-zinc-100">
                                                {{ $member->user?->name ?? 'Jugador' }}
                                                @if($member->is_captain)
                                                    <span class="ml-0.5 text-yellow-500">★</span>
                                                @endif
                                            </span>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $payBadge[$team->payment_status->value] ?? '' }}">
                                        {{ $team->payment_status->label() }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center text-gray-600 dark:text-gray-300">
                                    @if($team->amount_paid)
                                        ${{ number_format($team->amount_paid, 0, ',', '.') }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-wrap gap-2">
                                        @if($canManage && $team->payment_status->value === 'pending')
                                            <button wire:click="markAsPaid({{ $team->id }})"
                                                wire:confirm="¿Marcar a {{ $team->name }} como pagado manualmente?"
                                                class="text-xs px-2 py-1 rounded border border-green-300 dark:border-green-700 text-green-600 dark:text-green-400 hover:bg-green-50 dark:hover:bg-green-900/20">
                                                Marcar pagado
                                            </button>
                                        @endif
                                        @if($canManage)
                                            <button wire:click="withdrawTeam({{ $team->id }})"
                                                wire:confirm="{{ $withdrawConfirm }}"
                                                class="text-xs px-2 py-1 rounded border border-red-300 dark:border-red-700 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20">
                                                Dar de baja
                                            </button>
                                            @if(!$eligibleForRefund && $team->payment_status->value === 'paid')
                                                <span class="text-xs text-amber-600 dark:text-amber-400 italic">sin reembolso</span>
                                            @endif
                                        @endif
                                        @if(!$canManage)
                                            <span class="text-xs text-gray-400 dark:text-gray-500">-</span>
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
