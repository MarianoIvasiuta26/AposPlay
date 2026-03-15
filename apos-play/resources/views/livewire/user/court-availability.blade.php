<div>
    <!-- Filtros -->
    <div
        class="mb-6 bg-white dark:bg-neutral-800 rounded-xl border border-neutral-200 dark:border-neutral-700 p-4 shadow-sm">
        <h2 class="text-lg font-semibold mb-3 text-gray-900 dark:text-white">Filtros</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Filtro de fecha -->
            <div>
                <label for="date-filter"
                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Fecha</label>
                <select id="date-filter" wire:model="selectedDate" wire:change="updateSelectedDate($event.target.value)"
                    class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                    @foreach($availableDates as $dateOption)
                        <option value="{{ $dateOption['value'] }}">{{ $dateOption['label'] }}</option>
                    @endforeach
                </select>
            </div>
            <!-- Filtro de tipo de cancha -->
            <div>
                <label for="court-type-filter"
                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tipo de cancha</label>
                <select id="court-type-filter" wire:model="courtType" wire:change="updateCourtType($event.target.value)"
                    class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
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
                <div
                    class="overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 shadow-sm hover:shadow-md transition-shadow duration-300">
                    <div class="bg-green-600 dark:bg-green-700 text-white p-4">
                        <h2 class="text-xl font-semibold">{{ $court->name }}</h2>
                        <div class="flex justify-between items-center">
                            <p class="text-sm opacity-90">{{ $court->location ?? 'Sin ubicación' }}</p>
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-800 text-white">
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
                                            <svg class="h-4 w-4 mr-1 text-green-600 dark:text-green-500"
                                                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd"
                                                    d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            <span>{{ $hoursXCourts[$court->id][$selectedDate]['day_name'] }} -
                                                {{ \Carbon\Carbon::parse($selectedDate)->format('d/m/Y') }}</span>
                                        </h4>
                                        <div
                                            class="grid grid-cols-4 sm:grid-cols-5 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-2 mt-2">
                                            @foreach($hoursXCourts[$court->id][$selectedDate]['hours'] as $hourData)
                                                @php
                                                    $isReserved = isset($hourData['status']) && $hourData['status'] === 'reserved';
                                                    $isBlocked = isset($hourData['status']) && $hourData['status'] === 'blocked';
                                                @endphp

                                                @if($isBlocked)
                                                    <button disabled class="
                                                        bg-gray-200 text-gray-500 cursor-not-allowed border-gray-300 dark:bg-neutral-800 dark:text-neutral-600 dark:border-neutral-700
                                                        font-medium py-2 px-1 rounded-lg text-sm transition-colors border flex flex-col items-center justify-center
                                                    ">
                                                        <span class="font-bold">{{ $hourData['hour'] }}</span>
                                                        <span class="text-xs mt-1">No disponible</span>
                                                    </button>
                                                @elseif($isReserved)
                                                    <button disabled class="
                                                                                                                                bg-gray-100 text-gray-400 cursor-not-allowed border-gray-200 dark:bg-neutral-700 dark:text-neutral-500 dark:border-neutral-600
                                                                                                                                font-medium py-2 px-1 rounded-lg text-sm transition-colors border flex flex-col items-center justify-center
                                                                                                                            ">
                                                        <span class="font-bold">{{ $hourData['hour'] }}</span>
                                                        <span class="text-xs mt-1">Reservado</span>
                                                    </button>
                                                @else
                                                    <button
                                                        wire:click="openReservationModal({{ $court->id }}, {{ $hourData['schedule_id'] }}, '{{ $selectedDate }}', '{{ $hourData['hour'] }}')"
                                                        class="
                                                                                                                                    bg-blue-50 hover:bg-blue-100 text-blue-800 border-blue-200 dark:bg-blue-900/30 dark:hover:bg-blue-800/50 dark:text-blue-300 dark:border-blue-800
                                                                                                                                    font-medium py-2 px-1 rounded-lg text-sm transition-colors border flex flex-col items-center justify-center cursor-pointer
                                                                                                                                ">
                                                        <span class="font-bold">{{ $hourData['hour'] }}</span>
                                                        <span class="text-xs mt-1">Disponible</span>
                                                    </button>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                @else
                                    <p class="text-gray-500 dark:text-gray-400 italic">No hay horarios disponibles para esta cancha en
                                        la fecha seleccionada.</p>
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
        <div
            class="overflow-hidden rounded-xl border border-yellow-200 dark:border-yellow-900 bg-yellow-50 dark:bg-yellow-900/30 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400 dark:text-yellow-300" xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                            clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-yellow-700 dark:text-yellow-300">
                        @if($courtType !== 'all')
                            No hay canchas de tipo "{{ ucfirst($courtType) }}" disponibles para la fecha seleccionada.
                        @else
                            No hay canchas disponibles para la fecha seleccionada.
                        @endif
                        <button wire:click="resetFilters"
                            class="ml-2 underline hover:text-yellow-800 dark:hover:text-yellow-200">Restablecer
                            filtros</button>
                    </p>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal de Reserva -->
    @if($showReservationModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <!-- Background overlay -->
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"
                    wire:click="closeReservationModal"></div>

                <!-- Center modal -->
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div
                    class="inline-block align-bottom bg-white dark:bg-neutral-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white dark:bg-neutral-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div
                                class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 dark:bg-green-900 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-green-600 dark:text-green-400" xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white" id="modal-title">
                                    Confirmar Reserva
                                </h3>
                                <div class="mt-4 space-y-3">
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        Por favor verifica los detalles de tu reserva:
                                    </p>

                                    <div class="bg-gray-50 dark:bg-neutral-700 p-3 rounded-md">
                                        <p class="font-semibold text-gray-900 dark:text-gray-200">
                                            {{ $reservationCourtName }}
                                        </p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">
                                            {{ \Carbon\Carbon::parse($reservationDate)->format('d/m/Y') }} a las
                                            {{ $reservationTime }}
                                        </p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Precio por hora:
                                            ${{ number_format($reservationPrice, 0, ',', '.') }}</p>
                                    </div>

                                    <div>
                                        <label for="duration"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Duración
                                            (horas)</label>
                                        <select wire:model="reservationDuration" id="duration"
                                            class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white dark:bg-neutral-700 dark:border-neutral-600 dark:text-white rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                                            <option value="1">1 hora</option>
                                            <option value="2">2 horas</option>
                                            <option value="3">3 horas</option>
                                        </select>
                                    </div>

                                    {{-- Cupón de descuento --}}
                                    <div class="pt-2 border-t border-gray-100 dark:border-neutral-700 mt-3">
                                        @if($appliedCoupon)
                                            <div class="flex items-center justify-between bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-md px-3 py-2">
                                                <div>
                                                    <p class="text-sm font-medium text-green-800 dark:text-green-300">
                                                        Cupón <span class="font-mono">{{ $appliedCoupon->code }}</span> aplicado
                                                    </p>
                                                    <p class="text-xs text-green-600 dark:text-green-400">
                                                        {{ $appliedCoupon->description }} — {{ $appliedCoupon->formattedValue() }} de descuento
                                                    </p>
                                                </div>
                                                <button type="button" wire:click="removeCoupon"
                                                    class="text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-200 cursor-pointer ml-3 flex-shrink-0">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </div>
                                        @else
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                ¿Tenés un cupón de descuento?
                                            </label>
                                            <div class="flex gap-2">
                                                <input type="text" wire:model="couponCode"
                                                    placeholder="Ej: APOS-ABC123"
                                                    class="flex-1 rounded-md border-gray-300 dark:border-neutral-600 dark:bg-neutral-700 dark:text-white shadow-sm focus:border-green-500 focus:ring-green-500 text-sm uppercase">
                                                <button type="button" wire:click="applyCoupon"
                                                    class="px-3 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer flex-shrink-0">
                                                    Aplicar
                                                </button>
                                            </div>
                                            @error('couponCode')
                                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                            @enderror
                                        @endif
                                    </div>

                                    {{-- Canjear puntos --}}
                                    <div class="pt-2 border-t border-gray-100 dark:border-neutral-700 mt-3">
                                        @php
                                            $pointsRequired = config('loyalty.points_for_discount');
                                            $discountPct = config('loyalty.discount_percentage');
                                            $hasEnoughPoints = $userPointsBalance >= $pointsRequired;
                                        @endphp
                                        @if($hasEnoughPoints)
                                            <label class="flex items-center gap-2 cursor-pointer">
                                                <input type="checkbox" wire:model.live="usePoints"
                                                    class="rounded border-gray-300 text-yellow-600 shadow-sm focus:ring-yellow-500 dark:border-neutral-600 dark:bg-neutral-700">
                                                <span class="text-sm text-gray-700 dark:text-gray-300">
                                                    Usar {{ $pointsRequired }} puntos ({{ $discountPct }}% de descuento)
                                                </span>
                                            </label>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 ml-6">
                                                Saldo disponible: {{ $userPointsBalance }} puntos
                                            </p>
                                        @else
                                            <p class="text-sm text-gray-400 dark:text-gray-500">
                                                Necesitás {{ $pointsRequired }} puntos para obtener {{ $discountPct }}% de descuento (tenés {{ $userPointsBalance }})
                                            </p>
                                        @endif
                                    </div>

                                    {{-- Resumen de precio --}}
                                    <div class="text-right pt-2 border-t border-gray-100 dark:border-neutral-700 mt-3">
                                        @php
                                            $subtotal = $reservationPrice * $reservationDuration;
                                            $couponDisc = $discountAmount > 0
                                                ? $appliedCoupon->calculateDiscount($subtotal)
                                                : 0;
                                            $pointsDisc = ($usePoints && $hasEnoughPoints)
                                                ? round($subtotal * ($discountPct / 100), 2)
                                                : 0;
                                            $finalTotal = max(0, $subtotal - $couponDisc - $pointsDisc);
                                        @endphp
                                        @if($couponDisc > 0 || $pointsDisc > 0)
                                            <p class="text-sm text-gray-500 dark:text-gray-400 line-through">
                                                ${{ number_format($subtotal, 0, ',', '.') }}
                                            </p>
                                        @endif
                                        @if($couponDisc > 0)
                                            <p class="text-sm text-green-600 dark:text-green-400">
                                                − ${{ number_format($couponDisc, 0, ',', '.') }} cupón
                                            </p>
                                        @endif
                                        @if($pointsDisc > 0)
                                            <p class="text-sm text-yellow-600 dark:text-yellow-400">
                                                − ${{ number_format($pointsDisc, 0, ',', '.') }} puntos
                                            </p>
                                        @endif
                                        <p class="text-lg font-bold text-gray-900 dark:text-white">
                                            Total: ${{ number_format($finalTotal, 0, ',', '.') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-neutral-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="button" wire:click="confirmReservation"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm cursor-pointer">
                            Confirmar Reserva
                        </button>
                        <button type="button" wire:click="closeReservationModal"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm dark:bg-neutral-600 dark:text-gray-200 dark:border-neutral-500 dark:hover:bg-neutral-500 cursor-pointer">
                            Cancelar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>