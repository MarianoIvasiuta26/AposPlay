<div>
    <!-- Filtros -->
    <div class="mb-6 bg-white dark:bg-neutral-800 rounded-xl border border-neutral-200 dark:border-neutral-700 p-4 shadow-sm">
        <h2 class="text-lg font-semibold mb-3 text-gray-900 dark:text-white">Filtros</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Filtro de fecha -->
            <div>
                <label for="date-filter" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Fecha</label>
                <select id="date-filter" wire:model="selectedDate" wire:change="updateSelectedDate($event.target.value)" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                    @foreach($availableDates as $dateOption)
                        <option value="{{ $dateOption['value'] }}">{{ $dateOption['label'] }}</option>
                    @endforeach
                </select>
            </div>
            <!-- Filtro de tipo de cancha -->
            <div>
                <label for="court-type-filter" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tipo de cancha</label>
                <select id="court-type-filter" wire:model="courtType" wire:change="updateCourtType($event.target.value)" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                    <option value="all">Todas</option>
                    <option value="futbol">Fútbol</option>
                    <option value="padel">Pádel</option>
                </select>
            </div>
        </div>
    </div>
    
    @if(isset($filteredCourts) && count($filteredCourts) > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($filteredCourts as $court)
                <div class="overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 shadow-sm hover:shadow-md transition-shadow duration-300">
                    <div class="bg-green-600 dark:bg-green-700 text-white p-4">
                        <h2 class="text-xl font-semibold">{{ $court->name }}</h2>
                        <div class="flex justify-between items-center">
                            <p class="text-sm opacity-90">{{ $court->location ?? 'Sin ubicación' }}</p>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-800 text-white">
                                {{ ucfirst($court->type) }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="p-4">
                        <h3 class="font-semibold text-lg mb-3 text-gray-900 dark:text-white">Horarios Disponibles:</h3>
                        @if(isset($hoursXCourts[$court->id]) && count($hoursXCourts[$court->id]) > 0)
                            <div class="space-y-5">
                                @if(isset($hoursXCourts[$court->id][$selectedDate]))
                                    <div>
                                        <h4 class="font-medium text-gray-800 dark:text-gray-200 flex items-center">
                                            <svg class="h-4 w-4 mr-1 text-green-600 dark:text-green-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                                            </svg>
                                            <span>{{ $hoursXCourts[$court->id][$selectedDate]['day_name'] }} - {{ \Carbon\Carbon::parse($selectedDate)->format('d/m/Y') }}</span>
                                        </h4>
                                        <div class="grid grid-cols-4 sm:grid-cols-5 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-2 mt-2">
                                            @foreach($hoursXCourts[$court->id][$selectedDate]['hours'] as $hourData)
                                                <button class="bg-blue-50 hover:bg-blue-100 dark:bg-blue-900/30 dark:hover:bg-blue-800/50 text-blue-800 dark:text-blue-300 font-medium py-2 px-1 rounded-lg text-sm transition-colors border border-blue-200 dark:border-blue-800 flex flex-col items-center justify-center">
                                                    <span class="font-bold">{{ $hourData['hour'] }}</span>
                                                    <span class="text-xs text-blue-600 dark:text-blue-400 mt-1">Disponible</span>
                                                </button>
                                            @endforeach
                                        </div>
                                    </div>
                                @else
                                    <p class="text-gray-500 dark:text-gray-400 italic">No hay horarios disponibles para esta cancha en la fecha seleccionada.</p>
                                @endif
                            </div>
                        @else
                            <p class="text-gray-500 dark:text-gray-400 italic">No hay horarios disponibles para esta cancha.</p>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="overflow-hidden rounded-xl border border-yellow-200 dark:border-yellow-900 bg-yellow-50 dark:bg-yellow-900/30 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400 dark:text-yellow-300" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-yellow-700 dark:text-yellow-300">
                        @if($courtType !== 'all')
                            No hay canchas de tipo "{{ ucfirst($courtType) }}" disponibles para la fecha seleccionada.
                        @else
                            No hay canchas disponibles para la fecha seleccionada.
                        @endif
                        <button wire:click="resetFilters" class="ml-2 underline hover:text-yellow-800 dark:hover:text-yellow-200">Restablecer filtros</button>
                    </p>
                </div>
            </div>
        </div>
    @endif
</div>
