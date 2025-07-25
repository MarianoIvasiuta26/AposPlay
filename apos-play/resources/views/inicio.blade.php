<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>AposPlay</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

    <!-- Styles & Scripts Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased font-sans bg-gray-100 text-gray-900">
    <div class="min-h-screen flex flex-col">

        <!-- Header / Navbar -->
        <header class="bg-white shadow-sm py-4">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-between items-center">
                <!-- Logo o Nombre de la App -->
                <div class="flex-shrink-0">
                    <a href="{{ url('/') }}" class="text-2xl font-bold text-indigo-700">AposPlay</a>
                </div>

                <!-- Botones de Autenticación (Log In / Register) -->
                <nav class="flex items-center space-x-4">

                    @if (Route::has('login'))
                        <livewire:welcome.navigation />
                    @endif
                </nav>
            </div>
        </header>

        <!-- Main Content (Hero Section) -->
        <main class="flex-grow flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="flex flex-col items-center justify-center">
                    <h1 class="text-3xl font-bold text-indigo-700">Bienvenido a AposPlay</h1>
                    <p class="text-lg text-gray-600 mt-4">Gestiona tus turnos de cancha de manera eficiente y organizada.</p>
        </main>

        <!-- Footer -->
        <footer class="bg-gray-800 text-white py-6 text-center text-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                AposPlay © {{ date('Y') }} - Todos los derechos reservados.
            </div>
        </footer>

    </div>
</body>
</html>
