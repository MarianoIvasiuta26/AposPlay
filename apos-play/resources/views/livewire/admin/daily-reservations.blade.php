<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
            {{ __('Reservas del Día') }}
        </h2>

        <input type="date" wire:model.live="selectedDate"
            class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-zinc-900 dark:text-white dark:border-zinc-700">
    </div>

    @if($reservations->isEmpty())
        <div
            class="bg-white dark:bg-zinc-900 overflow-hidden shadow-sm sm:rounded-lg p-6 text-center text-gray-500 dark:text-gray-400">
            {{ __('No hay reservas para esta fecha.') }}
        </div>
    @else
        <div
            class="bg-white dark:bg-zinc-900 overflow-hidden shadow-sm sm:rounded-lg border border-gray-100 dark:border-zinc-800">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-zinc-700">
                    <thead class="bg-gray-50 dark:bg-zinc-800">
                        <tr>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Hora</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Cancha</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Usuario</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Estado</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Pago</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-zinc-900 divide-y divide-gray-200 dark:divide-zinc-700">
                        @foreach($reservations as $reservation)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">
                                    {{ \Carbon\Carbon::parse($reservation->start_time)->format('H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">
                                    {{ $reservation->court->name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">
                                    {{ $reservation->user->name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                                                                @if($reservation->status->value == 'confirmed') bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300
                                                                                                @elseif($reservation->status->value == 'cancelled') bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300
                                                                                                @else bg-gray-100 text-gray-800 @endif">
                                        {{ ucfirst($reservation->status->value) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ $reservation->amount_paid > 0 ? '$' . number_format($reservation->amount_paid, 0) : '-' }}
                                    @if($reservation->payment_status)
                                        <span class="text-xs ml-1">({{ $reservation->payment_status }})</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    @php
                                        $reservationStart = \Carbon\Carbon::parse($reservation->reservation_date->format('Y-m-d') . ' ' . $reservation->start_time);
                                        $hoursUntilStart = now()->diffInHours($reservationStart, false);
                                    @endphp

                                    @if($reservation->status->value == 'confirmed' && $reservation->payment_id && $hoursUntilStart >= 2)
                                        <button wire:click="confirmRefund({{ $reservation->id }})"
                                            class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 cursor-pointer">
                                            Reembolsar
                                        </button>
                                    @elseif($hoursUntilStart < 2 && $reservation->status->value == 'confirmed')
                                        <span class="text-gray-400 cursor-not-allowed" title="Menos de 2 horas">No
                                            reembolsable</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <flux:modal name="refund-modal" class="min-w-[20rem]" x-data
        x-on:open-modal.window="console.log($event.detail); if (($event.detail.name || $event.detail) === 'refund-modal') $el.showModal()"
        x-on:close-modal.window="if (($event.detail.name || $event.detail) === 'refund-modal') $el.close()">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Confirmar Reembolso</flux:heading>
                <flux:subheading>
                    @if($refundType === 'full')
                        Se realizará un reembolso <strong>TOTAL</strong> del pago.
                    @elseif($refundType === 'partial')
                        Se realizará un reembolso <strong>PARCIAL (50%)</strong> debido a que faltan menos de 8 horas.
                    @endif
                </flux:subheading>

                @if($reservationToRefund)
                    <div class="mt-4 p-4 bg-gray-50 dark:bg-zinc-800 rounded-md text-sm">
                        <p><strong>Cancha:</strong> {{ $reservationToRefund->court->name }}</p>
                        <p><strong>Usuario:</strong> {{ $reservationToRefund->user->name }}</p>
                        <p><strong>Monto Original:</strong> ${{ number_format($reservationToRefund->amount_paid, 0) }}</p>
                        <p class="mt-2 text-red-600 dark:text-red-400">
                            <strong>Monto a Reembolsar:</strong>
                            ${{ number_format($refundType === 'full' ? $reservationToRefund->amount_paid : $reservationToRefund->amount_paid * 0.5, 0) }}
                        </p>
                    </div>
                @endif
            </div>

            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button variant="ghost" class="cursor-pointer">Cancelar</flux:button>
                </flux:modal.close>
                <flux:button variant="danger" wire:click="processRefund" class="cursor-pointer">Confirmar Reembolso
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div>