<div>
    @if($sent)
        <div class="text-center py-12">
            <div class="w-16 h-16 mx-auto flex items-center justify-center rounded-full bg-green-500/20 mb-5">
                <svg class="w-8 h-8 text-green-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                </svg>
            </div>
            <h3 class="text-2xl font-bold text-white mb-3">¡Solicitud enviada!</h3>
            <p class="text-neutral-400 max-w-sm mx-auto">Recibimos tu solicitud. Nos pondremos en contacto con vos a la brevedad en <span class="text-white font-medium">{{ $contactEmail }}</span>.</p>
        </div>
    @else
        <div class="text-center mb-8">
            <div class="w-16 h-16 mx-auto flex items-center justify-center rounded-xl bg-red-600 text-white mb-5">
                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                </svg>
            </div>
            <h3 class="text-2xl font-bold text-white mb-3">Solicita tu cuenta Owner</h3>
            <p class="text-base text-neutral-400">Completa los datos de tu complejo y te crearemos una cuenta de administrador.</p>
        </div>

        <form wire:submit="submit" class="space-y-5">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-neutral-300 mb-2">Nombre del complejo</label>
                    <input wire:model="complexName" type="text" placeholder="Ej: Complejo Deportivo Norte"
                        class="w-full px-4 py-3 rounded-xl bg-neutral-900 border border-neutral-700 text-white placeholder-neutral-500 focus:border-green-500 focus:ring-1 focus:ring-green-500 outline-none transition">
                    @error('complexName') <p class="mt-1.5 text-sm text-red-400">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-neutral-300 mb-2">Ciudad</label>
                    <input wire:model="complexCity" type="text" placeholder="Ej: Buenos Aires"
                        class="w-full px-4 py-3 rounded-xl bg-neutral-900 border border-neutral-700 text-white placeholder-neutral-500 focus:border-green-500 focus:ring-1 focus:ring-green-500 outline-none transition">
                    @error('complexCity') <p class="mt-1.5 text-sm text-red-400">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-neutral-300 mb-2">Direccion</label>
                    <input wire:model="complexAddress" type="text" placeholder="Ej: Av. Libertador 1234"
                        class="w-full px-4 py-3 rounded-xl bg-neutral-900 border border-neutral-700 text-white placeholder-neutral-500 focus:border-green-500 focus:ring-1 focus:ring-green-500 outline-none transition">
                    @error('complexAddress') <p class="mt-1.5 text-sm text-red-400">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-neutral-300 mb-2">Cantidad y tipo de canchas</label>
                    <input wire:model="complexCourts" type="text" placeholder="Ej: 3 futbol, 2 padel"
                        class="w-full px-4 py-3 rounded-xl bg-neutral-900 border border-neutral-700 text-white placeholder-neutral-500 focus:border-green-500 focus:ring-1 focus:ring-green-500 outline-none transition">
                    @error('complexCourts') <p class="mt-1.5 text-sm text-red-400">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-neutral-300 mb-2">Nombre del responsable</label>
                    <input wire:model="contactName" type="text" placeholder="Tu nombre completo"
                        class="w-full px-4 py-3 rounded-xl bg-neutral-900 border border-neutral-700 text-white placeholder-neutral-500 focus:border-green-500 focus:ring-1 focus:ring-green-500 outline-none transition">
                    @error('contactName') <p class="mt-1.5 text-sm text-red-400">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-neutral-300 mb-2">Telefono de contacto</label>
                    <input wire:model="contactPhone" type="text" placeholder="Ej: +54 11 1234-5678"
                        class="w-full px-4 py-3 rounded-xl bg-neutral-900 border border-neutral-700 text-white placeholder-neutral-500 focus:border-green-500 focus:ring-1 focus:ring-green-500 outline-none transition">
                    @error('contactPhone') <p class="mt-1.5 text-sm text-red-400">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-neutral-300 mb-2">Email de contacto</label>
                <input wire:model="contactEmail" type="email" placeholder="tu@email.com"
                    class="w-full px-4 py-3 rounded-xl bg-neutral-900 border border-neutral-700 text-white placeholder-neutral-500 focus:border-green-500 focus:ring-1 focus:ring-green-500 outline-none transition">
                @error('contactEmail') <p class="mt-1.5 text-sm text-red-400">{{ $message }}</p> @enderror
            </div>

            @if($error)
                <p class="text-sm text-red-400 text-center">{{ $error }}</p>
            @endif

            <button type="submit"
                class="w-full flex items-center justify-center gap-2 px-6 py-4 text-lg font-bold rounded-xl bg-red-600 text-white hover:bg-red-500 transition cursor-pointer mt-8"
                wire:loading.attr="disabled" wire:loading.class="opacity-75">
                <svg wire:loading.remove class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                </svg>
                <svg wire:loading class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                <span wire:loading.remove>Enviar solicitud</span>
                <span wire:loading>Enviando...</span>
            </button>
        </form>
    @endif
</div>
