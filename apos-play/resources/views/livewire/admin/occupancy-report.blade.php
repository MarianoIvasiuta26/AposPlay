<div class="space-y-6">
    {{-- Header --}}
    <div class="flex justify-between items-center">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
            Reporte de Ocupación
        </h2>
    </div>

    {{-- Filters --}}
    <div class="bg-white dark:bg-zinc-900 overflow-hidden shadow-sm sm:rounded-lg border border-gray-100 dark:border-zinc-800 p-6 space-y-4">
        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Filtros</h3>

        {{-- Preset buttons --}}
        <div class="flex flex-wrap gap-2">
            <button wire:click="setPreset('today')"
                class="px-3 py-1.5 text-xs font-medium rounded-md border border-gray-300 dark:border-zinc-600 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-zinc-700 transition cursor-pointer">
                Hoy
            </button>
            <button wire:click="setPreset('week')"
                class="px-3 py-1.5 text-xs font-medium rounded-md border border-gray-300 dark:border-zinc-600 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-zinc-700 transition cursor-pointer">
                Esta semana
            </button>
            <button wire:click="setPreset('month')"
                class="px-3 py-1.5 text-xs font-medium rounded-md border border-gray-300 dark:border-zinc-600 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-zinc-700 transition cursor-pointer">
                Este mes
            </button>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- Date from --}}
            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Desde</label>
                <input type="date" wire:model.live="dateFrom"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-zinc-800 dark:text-white dark:border-zinc-700 text-sm">
            </div>

            {{-- Date to --}}
            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Hasta</label>
                <input type="date" wire:model.live="dateTo"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-zinc-800 dark:text-white dark:border-zinc-700 text-sm">
            </div>

            {{-- Court filter --}}
            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Cancha</label>
                <select wire:model.live="courtFilter"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-zinc-800 dark:text-white dark:border-zinc-700 text-sm">
                    <option value="all">Todas las canchas</option>
                    @foreach($courts as $court)
                        <option value="{{ $court->id }}">{{ $court->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Report type --}}
            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Agrupar por</label>
                <select wire:model.live="reportType"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-zinc-800 dark:text-white dark:border-zinc-700 text-sm">
                    <option value="cancha">Cancha</option>
                    <option value="horario">Franja horaria</option>
                    <option value="dia">Día de la semana</option>
                    <option value="semana">Semana</option>
                    <option value="mes">Mes</option>
                </select>
            </div>
        </div>
    </div>

    {{-- Summary cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white dark:bg-zinc-900 rounded-lg shadow-sm border border-gray-100 dark:border-zinc-800 p-5">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total reservas</p>
            <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $totalReservations }}</p>
        </div>

        <div class="bg-white dark:bg-zinc-900 rounded-lg shadow-sm border border-gray-100 dark:border-zinc-800 p-5">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Ingresos totales</p>
            <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-gray-100">
                ${{ number_format($totalIncome, 0, ',', '.') }}
            </p>
        </div>

        <div class="bg-white dark:bg-zinc-900 rounded-lg shadow-sm border border-gray-100 dark:border-zinc-800 p-5">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Período</p>
            <p class="mt-2 text-sm font-semibold text-gray-900 dark:text-gray-100">
                {{ \Carbon\Carbon::parse($dateFrom)->format('d/m/Y') }}
                —
                {{ \Carbon\Carbon::parse($dateTo)->format('d/m/Y') }}
            </p>
        </div>
    </div>

    {{-- Breakdown table --}}
    @if($breakdown->isEmpty())
        <div class="bg-white dark:bg-zinc-900 overflow-hidden shadow-sm sm:rounded-lg border border-gray-100 dark:border-zinc-800 p-10 text-center text-gray-500 dark:text-gray-400">
            No hay información disponible para los filtros seleccionados.
        </div>
    @else
        <div class="bg-white dark:bg-zinc-900 overflow-hidden shadow-sm sm:rounded-lg border border-gray-100 dark:border-zinc-800">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-zinc-700">
                    <thead class="bg-gray-50 dark:bg-zinc-800">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider w-1/3">
                                @switch($reportType)
                                    @case('cancha')  Cancha @break
                                    @case('horario') Franja horaria @break
                                    @case('dia')     Día @break
                                    @case('semana')  Semana @break
                                    @case('mes')     Mes @break
                                @endswitch
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Reservas
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Ingresos
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider w-1/3">
                                Ocupación relativa
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-zinc-900 divide-y divide-gray-200 dark:divide-zinc-700">
                        @foreach($breakdown as $row)
                            @php
                                $barWidth = $maxCount > 0 ? round($row['count'] / $maxCount * 100) : 0;
                                $share    = $totalReservations > 0 ? round($row['count'] / $totalReservations * 100) : 0;
                            @endphp
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                    {{ $row['label'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">
                                    {{ $row['count'] }}
                                    <span class="text-xs text-gray-400 ml-1">({{ $share }}%)</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">
                                    ${{ number_format($row['income'], 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <div class="flex-1 bg-gray-200 dark:bg-zinc-700 rounded-full h-2.5 overflow-hidden">
                                            <div class="bg-indigo-500 h-2.5 rounded-full transition-all duration-300"
                                                style="width: {{ $barWidth }}%"></div>
                                        </div>
                                        <span class="text-xs text-gray-500 dark:text-gray-400 w-9 text-right">{{ $barWidth }}%</span>
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
