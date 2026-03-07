<div class="p-6">
    <div class="max-w-4xl mx-auto">
        <flux:heading size="xl" class="mb-6">{{ __('Mis Puntos') }}</flux:heading>

        <div class="mb-8 rounded-lg border border-zinc-200 bg-zinc-50 p-6 dark:border-zinc-700 dark:bg-zinc-800">
            <div class="flex items-center gap-4">
                <flux:icon name="star" class="h-10 w-10 text-yellow-500" />
                <div>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ __('Saldo actual') }}</p>
                    <p class="text-3xl font-bold text-zinc-900 dark:text-white">{{ $balance }} {{ __('puntos') }}</p>
                </div>
            </div>
        </div>

        <flux:heading size="lg" class="mb-4">{{ __('Últimas transacciones') }}</flux:heading>

        @if($transactions->isEmpty())
            <p class="text-zinc-500 dark:text-zinc-400">{{ __('No tienes transacciones de puntos aún.') }}</p>
        @else
            <div class="overflow-x-auto rounded-lg border border-zinc-200 dark:border-zinc-700">
                <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                    <thead class="bg-zinc-50 dark:bg-zinc-800">
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('Tipo') }}</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('Puntos') }}</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('Descripción') }}</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('Fecha') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 bg-white dark:divide-zinc-700 dark:bg-zinc-900">
                        @foreach($transactions as $transaction)
                            <tr>
                                <td class="px-4 py-3 text-sm">
                                    @switch($transaction->type->value)
                                        @case('earned')
                                            <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800 dark:bg-green-900 dark:text-green-200">{{ __('Ganados') }}</span>
                                            @break
                                        @case('spent')
                                            <span class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-800 dark:bg-blue-900 dark:text-blue-200">{{ __('Canjeados') }}</span>
                                            @break
                                        @case('reversed')
                                            <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800 dark:bg-red-900 dark:text-red-200">{{ __('Revertidos') }}</span>
                                            @break
                                        @case('expired')
                                            <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-800 dark:bg-gray-900 dark:text-gray-200">{{ __('Expirados') }}</span>
                                            @break
                                    @endswitch
                                </td>
                                <td class="px-4 py-3 text-sm font-medium {{ $transaction->points > 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                    {{ $transaction->points > 0 ? '+' : '' }}{{ $transaction->points }}
                                </td>
                                <td class="px-4 py-3 text-sm text-zinc-700 dark:text-zinc-300">{{ $transaction->description }}</td>
                                <td class="px-4 py-3 text-sm text-zinc-500 dark:text-zinc-400">{{ $transaction->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
