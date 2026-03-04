<div class="space-y-6" x-data="{ noData: false }"
    x-on:no-data.window="noData = true; setTimeout(() => noData = false, 4000)">

    {{-- Header --}}
    <div class="flex justify-between items-center">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
            Exportar Ingresos
        </h2>
    </div>

    {{-- No data alert --}}
    <div x-show="noData" x-transition
        class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-700 text-yellow-800 dark:text-yellow-300 px-4 py-3 rounded-lg text-sm">
        No existen ingresos para el período seleccionado.
    </div>

    {{-- Filters --}}
    <div class="bg-white dark:bg-zinc-900 overflow-hidden shadow-sm sm:rounded-lg border border-gray-100 dark:border-zinc-800 p-6 space-y-5">
        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Período</h3>

        {{-- Filter mode toggle --}}
        <div class="flex gap-2">
            <button wire:click="$set('filterMode', 'month')"
                class="px-4 py-2 text-sm font-medium rounded-md transition cursor-pointer
                    {{ $filterMode === 'month'
                        ? 'bg-indigo-600 text-white'
                        : 'border border-gray-300 dark:border-zinc-600 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-zinc-700' }}">
                Por mes
            </button>
            <button wire:click="$set('filterMode', 'range')"
                class="px-4 py-2 text-sm font-medium rounded-md transition cursor-pointer
                    {{ $filterMode === 'range'
                        ? 'bg-indigo-600 text-white'
                        : 'border border-gray-300 dark:border-zinc-600 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-zinc-700' }}">
                Rango de fechas
            </button>
        </div>

        @if($filterMode === 'month')
            <div class="grid grid-cols-2 gap-4 max-w-sm">
                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Mes</label>
                    <select wire:model.live="selectedMonth"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-zinc-800 dark:text-white dark:border-zinc-700 text-sm">
                        <option value="1">Enero</option>
                        <option value="2">Febrero</option>
                        <option value="3">Marzo</option>
                        <option value="4">Abril</option>
                        <option value="5">Mayo</option>
                        <option value="6">Junio</option>
                        <option value="7">Julio</option>
                        <option value="8">Agosto</option>
                        <option value="9">Septiembre</option>
                        <option value="10">Octubre</option>
                        <option value="11">Noviembre</option>
                        <option value="12">Diciembre</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Año</label>
                    <input type="number" wire:model.live="selectedYear" min="2020" max="2099"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-zinc-800 dark:text-white dark:border-zinc-700 text-sm">
                </div>
            </div>
        @else
            <div class="grid grid-cols-2 gap-4 max-w-sm">
                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Desde</label>
                    <input type="date" wire:model.live="dateFrom"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-zinc-800 dark:text-white dark:border-zinc-700 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Hasta</label>
                    <input type="date" wire:model.live="dateTo"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-zinc-800 dark:text-white dark:border-zinc-700 text-sm">
                </div>
            </div>
        @endif
    </div>

    {{-- Summary cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-zinc-900 rounded-lg shadow-sm border border-gray-100 dark:border-zinc-800 p-5">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Registros</p>
            <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $totalRecords }}</p>
        </div>

        <div class="bg-white dark:bg-zinc-900 rounded-lg shadow-sm border border-gray-100 dark:border-zinc-800 p-5">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Ingresos brutos</p>
            <p class="mt-2 text-2xl font-bold text-green-600 dark:text-green-400">
                ${{ number_format($totalIncome, 0, ',', '.') }}
            </p>
        </div>

        <div class="bg-white dark:bg-zinc-900 rounded-lg shadow-sm border border-gray-100 dark:border-zinc-800 p-5">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Reembolsos</p>
            <p class="mt-2 text-2xl font-bold text-red-500 dark:text-red-400">
                ${{ number_format($totalRefunds, 0, ',', '.') }}
            </p>
        </div>

        <div class="bg-white dark:bg-zinc-900 rounded-lg shadow-sm border border-gray-100 dark:border-zinc-800 p-5">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Ingreso neto</p>
            <p class="mt-2 text-2xl font-bold text-indigo-600 dark:text-indigo-400">
                ${{ number_format($netIncome, 0, ',', '.') }}
            </p>
        </div>
    </div>

    {{-- Export buttons --}}
    <div class="bg-white dark:bg-zinc-900 overflow-hidden shadow-sm sm:rounded-lg border border-gray-100 dark:border-zinc-800 p-6">
        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-4">
            Formato de exportación
        </h3>

        @if($totalRecords === 0)
            <p class="text-sm text-gray-500 dark:text-gray-400 italic">
                No hay datos para el período seleccionado. Ajustá los filtros para exportar.
            </p>
        @else
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                Se exportarán <strong>{{ $totalRecords }}</strong> registros del período seleccionado.
            </p>

            <div class="flex flex-wrap gap-3">
                <button wire:click="exportCsv" wire:loading.attr="disabled"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-green-600 hover:bg-green-700 active:bg-green-800 text-white text-sm font-semibold rounded-md shadow transition cursor-pointer disabled:opacity-50">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    <span wire:loading.remove wire:target="exportCsv">Exportar CSV</span>
                    <span wire:loading wire:target="exportCsv">Generando...</span>
                </button>

                <button wire:click="exportPdf" wire:loading.attr="disabled"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-red-600 hover:bg-red-700 active:bg-red-800 text-white text-sm font-semibold rounded-md shadow transition cursor-pointer disabled:opacity-50">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                    <span wire:loading.remove wire:target="exportPdf">Exportar PDF</span>
                    <span wire:loading wire:target="exportPdf">Generando...</span>
                </button>
            </div>
        @endif
    </div>
</div>
