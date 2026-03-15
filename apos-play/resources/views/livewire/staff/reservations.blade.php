<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
            {{ __('Reservas del Dia') }}
        </h2>
    </div>

    @if(session()->has('success'))
        <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-300 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    @if(session()->has('error'))
        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 px-4 py-3 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    <div class="flex items-center gap-4">
        <input type="date" wire:model.live="selectedDate"
            class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-zinc-900 dark:text-white dark:border-zinc-700">
    </div>

    @if($reservations->isEmpty())
        <div class="bg-white dark:bg-zinc-900 overflow-hidden shadow-sm sm:rounded-lg p-12 text-center">
            <p class="text-gray-500 dark:text-gray-400 text-lg">No hay reservas para esta fecha.</p>
        </div>
    @else
        <div class="bg-white dark:bg-zinc-900 overflow-hidden shadow-sm sm:rounded-lg border border-gray-100 dark:border-zinc-800">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-zinc-700">
                    <thead class="bg-gray-50 dark:bg-zinc-800">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Hora</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Cancha</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Usuario</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Estado</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Pago</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-zinc-900 divide-y divide-gray-200 dark:divide-zinc-700">
                        @foreach($reservations as $reservation)
                            <tr class="hover:bg-gray-50 dark:hover:bg-zinc-800/50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">
                                    {{ \Carbon\Carbon::parse($reservation->start_time)->format('H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ $reservation->court->name ?? '—' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    <div>{{ $reservation->user->name ?? '—' }}</div>
                                    <div class="text-xs">{{ $reservation->user->email ?? '' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @switch($reservation->status->value)
                                            @case('paid') bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300 @break
                                            @case('confirmed') bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300 @break
                                            @case('pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300 @break
                                            @case('pending_payment') bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-300 @break
                                            @case('cancelled') bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300 @break
                                            @default bg-gray-100 text-gray-800 dark:bg-zinc-700 dark:text-gray-300
                                        @endswitch
                                    ">
                                        {{ ucfirst($reservation->status->value) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    @if($reservation->payment_status)
                                        {{ ucfirst($reservation->payment_status) }}
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center gap-3">
                                        @if($reservation->status === \App\Enums\ReservationStatus::PAID)
                                            <button wire:click="confirmReservation({{ $reservation->id }})"
                                                wire:confirm="¿Confirmar asistencia de esta reserva?"
                                                class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 cursor-pointer">
                                                Confirmar
                                            </button>
                                        @endif

                                        @if(in_array($reservation->status, [\App\Enums\ReservationStatus::PAID, \App\Enums\ReservationStatus::CONFIRMED]) && $reservation->payment_id)
                                            <button wire:click="confirmRefund({{ $reservation->id }})"
                                                class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 cursor-pointer">
                                                Reembolsar
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

    {{-- Refund confirmation modal --}}
    @if($showRefundModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
            <div class="bg-white dark:bg-zinc-900 rounded-lg shadow-xl p-6 max-w-md w-full mx-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-200 mb-4">
                    Confirmar Reembolso
                </h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
                    @if($refundType === 'full')
                        Se realizara un <strong>reembolso total</strong> (mas de 8 horas de anticipacion).
                    @else
                        Se realizara un <strong>reembolso parcial (50%)</strong> (menos de 8 horas de anticipacion).
                    @endif
                </p>
                <div class="flex justify-end gap-3">
                    <button wire:click="cancelRefund"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200 dark:bg-zinc-800 dark:text-gray-300 dark:hover:bg-zinc-700 cursor-pointer">
                        Cancelar
                    </button>
                    <button wire:click="processRefund"
                        class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700 cursor-pointer">
                        Confirmar Reembolso
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
