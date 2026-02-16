<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
            {{ __('Mis Reservas') }}
        </h2>
    </div>

    @if (session('success'))
        <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400"
            role="alert">
            <span class="font-medium">¡Éxito!</span> {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
            <span class="font-medium">Error:</span> {{ session('error') }}
        </div>
    @endif

    @if (session('warning'))
        <div class="p-4 mb-4 text-sm text-yellow-800 rounded-lg bg-yellow-50 dark:bg-gray-800 dark:text-yellow-300"
            role="alert">
            <span class="font-medium">Atención:</span> {{ session('warning') }}
        </div>
    @endif

    @if($reservations->isEmpty())
        <div
            class="bg-white dark:bg-zinc-900 overflow-hidden shadow-sm sm:rounded-lg p-6 text-center text-gray-500 dark:text-gray-400">
            {{ __('No tienes reservas registradas.') }}
        </div>
    @else
        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            @foreach($reservations as $reservation)
                <div
                    class="bg-white dark:bg-zinc-900 overflow-hidden shadow-sm sm:rounded-lg border border-gray-100 dark:border-zinc-800 relative group">
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 class="font-bold text-lg text-gray-900 dark:text-white">
                                    {{ $reservation->court->name }}
                                </h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $reservation->court->address->street ?? 'Dirección no disponible' }}
                                </p>
                            </div>
                            @php
                                $statusColors = [
                                    'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300',
                                    'confirmed' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
                                    'cancelled' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
                                ];
                                $statusLabels = [
                                    'pending' => 'Pendiente',
                                    'pending_payment' => 'Pendiente de Pago',
                                    'confirmed' => 'Confirmada',
                                    'cancelled' => 'Cancelada',
                                ];
                                $status = $reservation->status->value;
                            @endphp
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$status] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ $statusLabels[$status] ?? ucfirst($status) }}
                            </span>
                        </div>

                        <div class="space-y-2 mb-6">
                            <div class="flex items-center text-gray-600 dark:text-gray-300">
                                <flux:icon name="calendar-days" class="w-4 h-4 mr-2" />
                                <span>{{ $reservation->reservation_date->translatedFormat('l j \d\e F, Y') }}</span>
                            </div>
                            <div class="flex items-center text-gray-600 dark:text-gray-300">
                                <flux:icon name="clock" class="w-4 h-4 mr-2" />
                                <span>{{ \Carbon\Carbon::parse($reservation->start_time)->format('H:i') }} -
                                    {{ \Carbon\Carbon::parse($reservation->start_time)->addHours($reservation->duration_hours ?? 1)->format('H:i') }}</span>
                            </div>
                            <div class="flex items-center text-gray-600 dark:text-gray-300">
                                <flux:icon name="banknotes" class="w-4 h-4 mr-2" />
                                <span>${{ number_format($reservation->total_price, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        @php
                            $reservationStart = \Carbon\Carbon::parse($reservation->reservation_date->format('Y-m-d') . ' ' . $reservation->start_time);
                            $canCancel = in_array($status, ['pending', 'confirmed', 'pending_payment']) && now()->addHours(24)->lte($reservationStart);
                        @endphp

                        @if(in_array($status, ['pending', 'confirmed', 'pending_payment']))
                            <div class="pt-4 border-t border-gray-100 dark:border-zinc-800 flex gap-2">
                                @if(in_array($status, ['pending', 'pending_payment']))
                                    <button wire:click="pay({{ $reservation->id }})"
                                        class="w-full inline-flex justify-center items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150 cursor-pointer">
                                        Pagar Reserva
                                    </button>
                                @endif

                                @if($canCancel)
                                    <button wire:click="cancel({{ $reservation->id }})"
                                        wire:confirm="¿Estás seguro que deseas cancelar esta reserva?"
                                        class="w-full inline-flex justify-center items-center px-4 py-2 bg-white dark:bg-zinc-800 border border-red-300 dark:border-red-700 rounded-md font-semibold text-xs text-red-700 dark:text-red-400 uppercase tracking-widest shadow-sm hover:bg-red-50 dark:hover:bg-red-900/20 disabled:opacity-25 transition ease-in-out duration-150 cursor-pointer">
                                        Cancelar Reserva
                                    </button>
                                @else
                                    <div
                                        class="w-full text-center text-xs text-gray-500 dark:text-gray-400 italic flex items-center justify-center">
                                        No se puede cancelar (menos de 24hs)
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>