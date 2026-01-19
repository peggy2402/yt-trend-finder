<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>ZT GROUP | SHOP TÀI NGUYÊN VIA</title>

        <!-- 1. Fix Font: Load font Outfit -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

        <!-- 2. Fix Icons: Load Font Awesome 6.5.1 -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <link rel="shortcut icon" href="{{ asset('images/logo.png')}}" type="image/x-icon">
        <!-- Tailwind CDN (Optional fallback if Vite is slow in dev) -->
        <script src="https://cdn.tailwindcss.com"></script>

        <style>
            body { font-family: 'Outfit', sans-serif; }
        </style>
    </head>
    <body class="font-sans antialiased bg-[#f3f4f6]">
        <div class="min-h-screen">
            <!-- Load thanh điều hướng Navigation -->
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-slate-800 shadow-sm border-b border-slate-700">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>
    </body>
</html>