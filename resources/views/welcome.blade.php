<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'SKSU') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
        <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;500;700&display=swap" rel="stylesheet">

        <!-- Styles / Scripts -->
        @if (file_exists(public_path('build/manifest.json')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            @if (file_exists(public_path('css/welcome-fallback.css')))
                <!-- External fallback CSS -->
                <link rel="stylesheet" href="{{ asset('css/welcome-fallback.css') }}">
            @else
                <!-- Inline fallback CSS when Vite and fallback file are not available -->
                <style>
                    body {
                        font-family: 'Noto Sans JP', 'Instrument Sans', sans-serif;
                        margin: 0;
                        padding: 0;
                    }

                    .flex {
                        display: flex;
                    }

                    .min-h-screen {
                        min-height: 100vh;
                    }

                    .left-panel {
                        width: 40%;
                        padding: 2rem;
                        display: flex;
                        flex-direction: column;
                        justify-content: center;
                        position: relative;
                        overflow: hidden;
                        background: linear-gradient(135deg, #f59e0b 0%, #fef3c7 100%);
                    }

                    .right-panel {
                        width: 60%;
                        background-size: cover;
                        background-position: center;
                    }

                    h1 {
                        font-size: 2.25rem;
                        font-weight: 700;
                        color: #1f2937;
                        margin-bottom: 2rem;
                    }

                    .nav-links {
                        display: flex;
                        flex-direction: column;
                        gap: 1rem;
                        max-width: 300px;
                    }

                    .nav-links a {
                        display: block;
                        width: 100%;
                        padding: 0.75rem 1rem;
                        background-color: #ffffff;
                        border-radius: 0.5rem;
                        text-align: center;
                        font-weight: 500;
                        color: #1f2937;
                        text-decoration: none;
                        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
                        transition: all 0.2s ease;
                    }

                    .nav-links a:hover {
                        background-color: #1f2937;
                        color: #ffffff;
                    }

                    @media (max-width: 768px) {
                        .flex {
                            flex-direction: column;
                        }

                        .left-panel, .right-panel {
                            width: 100%;
                        }

                        .left-panel {
                            min-height: 60vh;
                        }

                        .right-panel {
                            min-height: 40vh;
                        }
                    }
                </style>
            @endif
        @endif
    </head>
    <body class="antialiased">
        <div class="flex min-h-screen">
            <div class="left-panel w-2/5 p-8 flex flex-col justify-center relative overflow-hidden bg-gradient-to-br from-amber-500 to-amber-100 dark:from-amber-600 dark:to-amber-900">
                <!-- Logo -->
                <div class="mb-6">
                    <a href="/">
                        <x-application-logo class="w-24 h-24" />
                    </a>
                </div>

                <h1 class="text-4xl md:text-5xl font-bold mb-8 text-gray-800 dark:text-white">
                    立入禁止区域
                </h1>

                <!-- Nav Links -->
                @if (Route::has('login'))
                    <div class="nav-links space-y-4">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="block w-full px-4 py-3 bg-white dark:bg-gray-800 rounded-lg text-center font-medium text-gray-800 dark:text-white hover:bg-gray-800 hover:text-white dark:hover:bg-white dark:hover:text-gray-800 transition duration-200 shadow-md">
                                ダッシュボード
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="block w-full px-4 py-3 bg-white dark:bg-gray-800 rounded-lg text-center font-medium text-gray-800 dark:text-white hover:bg-gray-800 hover:text-white dark:hover:bg-white dark:hover:text-gray-800 transition duration-200 shadow-md">
                                関係者以外立ち入り禁止
                            </a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="block w-full px-4 py-3 bg-white dark:bg-gray-800 rounded-lg text-center font-medium text-gray-800 dark:text-white hover:bg-gray-800 hover:text-white dark:hover:bg-white dark:hover:text-gray-800 transition duration-200 shadow-md">
                                    新規登録
                                </a>
                            @endif
                        @endauth

                        <a href="{{ route('events.index') }}" class="block w-full px-4 py-3 bg-white dark:bg-gray-800 rounded-lg text-center font-medium text-gray-800 dark:text-white hover:bg-gray-800 hover:text-white dark:hover:bg-white dark:hover:text-gray-800 transition duration-200 shadow-md">
                            廃イベント記録表（破損あり）
                        </a>
                        <a href="https://laravel.com/docs" target="_blank" class="block w-full px-4 py-3 bg-white dark:bg-gray-800 rounded-lg text-center font-medium text-gray-800 dark:text-white hover:bg-gray-800 hover:text-white dark:hover:bg-white dark:hover:text-gray-800 transition duration-200 shadow-md">
                            最後の通信
                        </a>
                        <a href="https://laracasts.com" target="_blank" class="block w-full px-4 py-3 bg-white dark:bg-gray-800 rounded-lg text-center font-medium text-gray-800 dark:text-white hover:bg-gray-800 hover:text-white dark:hover:bg-white dark:hover:text-gray-800 transition duration-200 shadow-md">
                            Outcasts
                        </a>
                        <a href="https://cloud.laravel.com" target="_blank" class="block w-full px-4 py-3 bg-white dark:bg-gray-800 rounded-lg text-center font-medium text-gray-800 dark:text-white hover:bg-gray-800 hover:text-white dark:hover:bg-white dark:hover:text-gray-800 transition duration-200 shadow-md">
                            Assimilate
                        </a>
                    </div>
                @endif

                <!-- Decorative elements -->
                <div class="absolute top-[10%] left-[10%] w-36 h-36 rounded-full bg-white/30 -z-10"></div>
                <div class="absolute top-1/2 right-[15%] w-20 h-20 rounded-full bg-white/30 -z-10"></div>
                <div class="absolute bottom-[10%] left-[20%] w-28 h-28 rounded-full bg-white/30 -z-10"></div>
                <div class="absolute top-[20%] right-[20%] w-16 h-16 rounded-full bg-white/30 -z-10"></div>
            </div>

            <div class="right-panel w-3/5 bg-cover bg-center"
                 style="background-image: url('{{ asset('images/background.webp') }}');"
                 role="img"
                 aria-label="学校の背景画像">
            </div>
        </div>
    </body>
</html>
