<x-layouts.app :title="__('AposPlay - Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl">

        <!-- Acciones rápidas -->
        <div class="grid auto-rows-min gap-4 md:grid-cols-3">

            <!-- Mis canchas -->
            <a
                href="{{ route('canchas') }}"
                class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 hover:shadow-md transition"
            >
                <div class="absolute inset-0 p-6 flex flex-col justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                            Mis canchas
                        </h3>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            Administrá y creá tus canchas
                        </p>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                            Administrador
                        </span>

                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A2 2 0 013 15.382V6.618a2 2 0 011.553-1.894L9 2m0 18l6-3m-6 3V2m6 15l5.447-2.724A2 2 0 0021 15.382V6.618a2 2 0 00-1.553-1.894L15 2m0 15V2m0 0L9 5" />
                        </svg>
                    </div>
                </div>
            </a>

            <!-- Card futura -->
            <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
            </div>

            <!-- Card futura -->
            <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
            </div>

        </div>

        <!-- Sección inferior -->
        <div class="relative h-full flex-1 overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
            <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
        </div>

    </div>
</x-layouts.app>
