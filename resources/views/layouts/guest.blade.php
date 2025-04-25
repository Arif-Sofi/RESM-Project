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
        <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;500;700&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Improved styles for better layout -->
        <style>
            html, body {
                width: 100%;
                height: 100%;
                margin: 0;
                padding: 0;
                overflow-x: hidden;
                font-family: 'Figtree', 'Noto Sans JP', sans-serif;
            }

            /* Fix for body in standard view */
            body:not(.auth-split-screen) {
                display: block;
            }

            /* Special handling for login page with split screen */
            body.auth-split-screen {
                display: flex;
            }

            /* Standard authentication container */
            .auth-container {
                min-height: 100vh;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                padding: 1rem;
                background-color: #f3f4f6;
            }

            .auth-card {
                width: 100%;
                max-width: 32rem;
                padding: 2rem;
                background-color: white;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
                border-radius: 0.5rem;
            }
        </style>
    </head>
    <body class="font-sans text-gray-900 antialiased {{ Route::currentRouteName() === 'login' ? 'auth-split-screen' : '' }}">
        @if(Route::currentRouteName() === 'login')
            <!-- Special layout for login page with split screen -->
            {{ $slot }}
        @else
            <!-- Standard centered layout for other auth pages -->
            <div class="auth-container bg-gray-100">
                <div class="mb-6">
                    <a href="/">
                        <x-application-logo class="w-20 h-20" />
                    </a>
                </div>
                <div class="auth-card">
                    {{ $slot }}
                </div>
            </div>
        @endif
    </body>
</html>
