<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Disponibilidad de Canchas') }}
        </h2>
    </x-slot>

    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if(count($courts) > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($courts as $court)
                                <div class="bg-white rounded-lg shadow-md overflow-hidden border">
                                    <div class="bg-green-600 text-white p-4">
                                        <h2 class="text-xl font-semibold">{{ $court->name }}</h2>
                                        <p class="text-sm">{{ $court->location }}</p>
                                    </div>
                                    
                                    <div class="p-4">
                                        <h3 class="font-semibold text-lg mb-2">Horarios Disponibles:</h3>
                                        
                                        @if(isset($available_hours_with_dates[$court->id]) && count($available_hours_with_dates[$court->id]) > 0)
                                            <div class="space-y-4">
                                                @php
                                                    // Agrupar horas por fecha
                                                    $hoursByDate = [];
                                                    foreach($available_hours_with_dates[$court->id] as $hourData) {
                                                        if(!isset($hoursByDate[$hourData['date']])) {
                                                            $hoursByDate[$hourData['date']] = [
                                                                'day_name' => $hourData['day_name'],
                                                                'date' => $hourData['date'],
                                                                'hours' => []
                                                            ];
                                                        }
                                                        $hoursByDate[$hourData['date']]['hours'][] = $hourData;
                                                    }
                                                    
                                                    // Ordenar por fecha
                                                    ksort($hoursByDate);
                                                @endphp
                                                
                                                @foreach($hoursByDate as $date => $dateData)
                                                    <div class="border-t pt-3 first:border-t-0 first:pt-0">
                                                        <h4 class="font-medium text-gray-800">
                                                            {{ $dateData['day_name'] }} - {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}
                                                        </h4>
                                                        <div class="flex flex-wrap gap-2 mt-2">
                                                            @foreach($dateData['hours'] as $hourData)
                                                                <button class="bg-blue-100 hover:bg-blue-200 text-blue-800 font-medium py-1 px-3 rounded-full text-sm transition-colors">
                                                                    {{ $hourData['hour'] }}
                                                                </button>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <p class="text-gray-500 italic">No hay horarios disponibles para esta cancha.</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-yellow-700">
                                        No hay canchas disponibles con al menos 2 horas continuas en este momento.
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
