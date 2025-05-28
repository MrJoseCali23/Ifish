<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-white">

    {{-- Navbar superior --}}
    @include('layouts.navigation') {{-- esto ya existe gracias a Breeze --}}

    {{-- Navbar lateral, si lo tenés separado --}}
    @includeIf('partials.navbar')

    <main class="p-6">
        {{ $slot }}
    </main>
</body>
</html>
