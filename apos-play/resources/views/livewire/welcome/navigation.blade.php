<nav class="-mx-3 flex flex-1 justify-end items-center gap-x-3"> {{-- Asegúrate de que gap-x-3 esté aquí --}}
    @auth
        <a
            href="{{ url('/dashboard') }}"
            class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out"
        >
            Dashboard
        </a>
    @else
        <a
            href="{{ route('login') }}"
            class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out"
            >
            Iniciar Sesión
        </a>

        @if (Route::has('register'))
            <a
                href="{{ route('register') }}"
            class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out"
            >
                Registrarse
            </a>
        @endif
    @endauth
</nav>
