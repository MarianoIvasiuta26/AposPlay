<div class="space-y-6">
    {{-- Header --}}
    <div class="flex justify-between items-center">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
            {{ __('Cupones y Descuentos') }}
        </h2>

        @if(!$showCreateForm)
            <button wire:click="openCreateForm"
                class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500 active:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:focus:ring-offset-zinc-800 transition ease-in-out duration-150 cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Crear cupón
            </button>
        @endif
    </div>

    {{-- Flash messages --}}
    @if(session()->has('success'))
        <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-300 px-4 py-3 rounded-lg flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- CREATE FORM --}}
    @if($showCreateForm)
        <div class="bg-white dark:bg-zinc-900 overflow-hidden shadow-sm sm:rounded-lg border border-gray-100 dark:border-zinc-800">
            <div class="p-6 space-y-6">
                <div class="flex items-center justify-between border-b border-gray-200 dark:border-zinc-700 pb-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Nuevo Cupón</h3>
                    <button wire:click="cancelCreate" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form wire:submit.prevent="saveCoupon" class="space-y-6">
                    {{-- Tipo de descuento --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Tipo de descuento
                            </label>
                            <select wire:model.live="type"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-zinc-800 dark:text-white dark:border-zinc-700">
                                <option value="percentage">Porcentaje (%)</option>
                                <option value="fixed_amount">Monto Fijo ($)</option>
                            </select>
                            @error('type') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Valor del descuento
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500 dark:text-gray-400">
                                    {{ $type === 'percentage' ? '%' : '$' }}
                                </span>
                                <input type="number" wire:model="value" step="0.01" min="0.01"
                                    {{ $type === 'percentage' ? 'max=100' : '' }}
                                    placeholder="{{ $type === 'percentage' ? 'Ej: 15' : 'Ej: 500' }}"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-zinc-800 dark:text-white dark:border-zinc-700 pl-8">
                            </div>
                            @error('value') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    {{-- Descripción --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Descripción del cupón
                        </label>
                        <textarea wire:model="description" rows="3"
                            placeholder="Ej: Descuento por ser cliente frecuente"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-zinc-800 dark:text-white dark:border-zinc-700"></textarea>
                        @error('description') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    {{-- Fechas --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Válido desde
                            </label>
                            <input type="date" wire:model="validFrom"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-zinc-800 dark:text-white dark:border-zinc-700">
                            @error('validFrom') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Válido hasta <span class="text-gray-400">(opcional)</span>
                            </label>
                            <input type="date" wire:model="validUntil"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-zinc-800 dark:text-white dark:border-zinc-700">
                            @error('validUntil') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Máximo de usos <span class="text-gray-400">(opcional)</span>
                            </label>
                            <input type="number" wire:model="maxUses" min="1"
                                placeholder="Ilimitado"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-zinc-800 dark:text-white dark:border-zinc-700">
                            @error('maxUses') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    {{-- Selección de clientes --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Clientes que reciben el cupón
                        </label>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">
                            Seleccioná los clientes a los que aplica este cupón. Se les enviará una notificación por email.
                        </p>

                        <div class="border border-gray-200 dark:border-zinc-700 rounded-md max-h-60 overflow-y-auto">
                            {{-- Select all --}}
                            <div class="sticky top-0 bg-gray-50 dark:bg-zinc-800 px-4 py-2 border-b border-gray-200 dark:border-zinc-700">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox"
                                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:border-zinc-600 dark:bg-zinc-700"
                                        @if(count($selectedUsers) === count($availableUsers)) checked @endif
                                        wire:click="$set('selectedUsers', {{ count($selectedUsers) === count($availableUsers) ? '[]' : $availableUsers->pluck('id')->toJson() }})">
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Seleccionar todos ({{ count($availableUsers) }})
                                    </span>
                                </label>
                            </div>

                            @forelse($availableUsers as $availableUser)
                                <label class="flex items-center gap-3 px-4 py-2 hover:bg-gray-50 dark:hover:bg-zinc-800 cursor-pointer border-b border-gray-100 dark:border-zinc-800 last:border-b-0">
                                    <input type="checkbox" value="{{ $availableUser->id }}" wire:model="selectedUsers"
                                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:border-zinc-600 dark:bg-zinc-700">
                                    <div>
                                        <span class="text-sm text-gray-900 dark:text-gray-200">{{ $availableUser->name }}</span>
                                        <span class="text-xs text-gray-500 dark:text-gray-400 ml-2">{{ $availableUser->email }}</span>
                                    </div>
                                </label>
                            @empty
                                <div class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400 text-center">
                                    No hay clientes registrados.
                                </div>
                            @endforelse
                        </div>

                        @error('selectedUsers') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror

                        @if(count($selectedUsers) > 0)
                            <p class="text-xs text-green-600 dark:text-green-400 mt-2">
                                {{ count($selectedUsers) }} cliente(s) seleccionado(s)
                            </p>
                        @endif
                    </div>

                    {{-- Submit --}}
                    <div class="flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-zinc-700">
                        <button type="button" wire:click="cancelCreate"
                            class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-zinc-800 border border-gray-300 dark:border-zinc-600 rounded-md shadow-sm hover:bg-gray-50 dark:hover:bg-zinc-700 cursor-pointer">
                            Cancelar
                        </button>
                        <button type="submit"
                            class="px-4 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-md shadow-sm hover:bg-green-500 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:focus:ring-offset-zinc-800 cursor-pointer">
                            Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- COUPON LIST --}}
    @if(!$showCreateForm)
        {{-- Search --}}
        <div class="flex items-center gap-4">
            <div class="relative flex-1 max-w-md">
                <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input type="text" wire:model.live.debounce.300ms="search"
                    placeholder="Buscar por código o descripción..."
                    class="w-full pl-10 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-zinc-900 dark:text-white dark:border-zinc-700">
            </div>
        </div>

        @if($coupons->isEmpty())
            <div class="bg-white dark:bg-zinc-900 overflow-hidden shadow-sm sm:rounded-lg p-12 text-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-300 dark:text-zinc-600 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                </svg>
                <p class="text-gray-500 dark:text-gray-400 text-lg">No hay cupones creados aún.</p>
                <p class="text-gray-400 dark:text-gray-500 text-sm mt-1">Creá tu primer cupón para ofrecer descuentos a tus clientes.</p>
            </div>
        @else
            <div class="bg-white dark:bg-zinc-900 overflow-hidden shadow-sm sm:rounded-lg border border-gray-100 dark:border-zinc-800">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-zinc-700">
                        <thead class="bg-gray-50 dark:bg-zinc-800">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Código</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Descripción</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Descuento</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Clientes</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Usos</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Validez</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Estado</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-zinc-900 divide-y divide-gray-200 dark:divide-zinc-700">
                            @foreach($coupons as $coupon)
                                <tr class="hover:bg-gray-50 dark:hover:bg-zinc-800/50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-mono font-bold bg-indigo-50 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800">
                                            {{ $coupon->code }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-200 max-w-[200px] truncate">
                                        {{ $coupon->description }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold
                                            {{ $coupon->type->value === 'percentage' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300' : 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300' }}">
                                            {{ $coupon->formattedValue() }}
                                            <span class="ml-1 font-normal opacity-75">{{ $coupon->type->label() }}</span>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ $coupon->users_count }} cliente(s)
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ $coupon->times_used }}{{ $coupon->max_uses ? ' / ' . $coupon->max_uses : '' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        <div>{{ $coupon->valid_from->format('d/m/Y') }}</div>
                                        @if($coupon->valid_until)
                                            <div class="text-xs">hasta {{ $coupon->valid_until->format('d/m/Y') }}</div>
                                        @else
                                            <div class="text-xs text-green-500">Sin expiración</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @if($coupon->is_active)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">
                                                Activo
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-zinc-700 dark:text-gray-300">
                                                Inactivo
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex items-center gap-3">
                                            <button wire:click="toggleStatus({{ $coupon->id }})"
                                                class="{{ $coupon->is_active ? 'text-yellow-600 hover:text-yellow-800 dark:text-yellow-400 dark:hover:text-yellow-300' : 'text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-300' }} cursor-pointer"
                                                title="{{ $coupon->is_active ? 'Desactivar' : 'Activar' }}">
                                                {{ $coupon->is_active ? 'Desactivar' : 'Activar' }}
                                            </button>
                                            <button wire:click="deleteCoupon({{ $coupon->id }})"
                                                wire:confirm="¿Estás seguro de que querés eliminar este cupón?"
                                                class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 cursor-pointer">
                                                Eliminar
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    @endif

    {{-- Confirmation modal --}}
    <flux:modal name="confirm-coupon-modal" class="min-w-[22rem]" x-data
        x-on:open-modal.window="if (($event.detail.name || $event.detail) === 'confirm-coupon-modal') $el.showModal()"
        x-on:close-modal.window="if (($event.detail.name || $event.detail) === 'confirm-coupon-modal') $el.close()">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Confirmar creación del cupón</flux:heading>
                <flux:subheading>
                    ¿Estás seguro de que querés crear este cupón?
                </flux:subheading>

                <div class="mt-4 p-4 bg-gray-50 dark:bg-zinc-800 rounded-md text-sm space-y-1">
                    <p><strong>Tipo:</strong> {{ $type === 'percentage' ? 'Porcentaje' : 'Monto Fijo' }}</p>
                    <p><strong>Valor:</strong> {{ $type === 'percentage' ? $value . '%' : '$' . $value }}</p>
                    <p><strong>Descripción:</strong> {{ $description }}</p>
                    <p><strong>Clientes:</strong> {{ count($selectedUsers) }} seleccionado(s)</p>
                    @if($validUntil)
                        <p><strong>Válido hasta:</strong> {{ $validUntil }}</p>
                    @endif
                </div>

                <div class="mt-3 p-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-md">
                    <p class="text-xs text-blue-700 dark:text-blue-300">
                        <strong>Nota:</strong> Se enviará una notificación por email a los {{ count($selectedUsers) }} cliente(s) seleccionado(s) informándoles del cupón.
                    </p>
                </div>
            </div>

            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button variant="ghost" class="cursor-pointer">Cancelar</flux:button>
                </flux:modal.close>
                <flux:button variant="primary" wire:click="confirmSave" class="cursor-pointer">Confirmar y Crear</flux:button>
            </div>
        </div>
    </flux:modal>
</div>
