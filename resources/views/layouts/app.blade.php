<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- 全幅表示のためのスタイル -->
        <style>
            /* Alpine.js x-cloak directive - prevents flash during initialization */
            [x-cloak] {
                display: none !important;
            }

            html, body {
                width: 100%;
                height: 100%;
                margin: 0;
                padding: 0;
                overflow-x: hidden;
            }

            .min-h-screen {
                width: 100%;
            }

            main {
                width: 100%;
                min-height: calc(100vh - 4rem); /* ナビゲーションバーの高さを引いた高さ */
            }
        </style>
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-base dark:bg-primary w-full">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-base dark:bg-primary shadow w-full">
                    <div class="max-w-[1920px] mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main class="w-full">
                {{ $slot }}
            </main>
        </div>
        @stack('scripts')
    </body>
</html>
