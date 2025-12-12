<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'SCG SCM Dashboard') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-50">
        <div class="w-full sm:max-w-md px-6 py-8 bg-white shadow-md overflow-hidden sm:rounded-lg">
            <div class="flex justify-center mb-8">
                <div class="text-center">
                    <div class="flex justify-center mb-4">
                        <img src="{{ asset('images/scg_logo.png') }}" alt="SCG Logo" class="h-16">
                    </div>
                    <h1 class="text-2xl font-bold text-[#A6192E] mb-1">
                        {{ config('app.name', 'SCG SCM') }}
                    </h1>
                    <p class="text-sm text-gray-600">Supply Chain Management System</p>
                </div>
            </div>

            @yield('content')
        </div>
    </div>
</body>
</html>
