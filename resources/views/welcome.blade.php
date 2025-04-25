<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
        <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;500;700&display=swap" rel="stylesheet">

        <!-- Styles / Scripts -->
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <style>
                /* Base styles */
                body {
                    font-family: 'Noto Sans JP', 'Instrument Sans', sans-serif;
                    margin: 0;
                    padding: 0;
                    width: 100%;
                    height: 100vh;
                    display: flex;
                    overflow-x: hidden;
                }

                .left-panel {
                    width: 40%;
                    background: linear-gradient(135deg, #ffa500 0%, #ffedcc 100%);
                    padding: 2rem;
                    display: flex;
                    flex-direction: column;
                    justify-content: center;
                    position: relative;
                    overflow: hidden;
                }

                .right-panel {
                    width: 60%;
                    background-color: #4a89dc;
                    background-size: cover;
                    background-position: center;
                }

                h1 {
                    font-size: 2.5rem;
                    font-weight: 700;
                    color: #333;
                    margin-bottom: 2rem;
                }

                .nav-links {
                    display: flex;
                    flex-direction: column;
                    gap: 1rem;
                    margin-top: 2rem;
                    width: 100%;
                    max-width: 300px;
                }

                .nav-link {
                    display: inline-block;
                    padding: 0.75rem 1.5rem;
                    border-radius: 8px;
                    background-color: #ffffff;
                    color: #333;
                    text-decoration: none;
                    text-align: center;
                    font-weight: 500;
                    transition: all 0.3s ease;
                    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                }

                .nav-link:hover {
                    background-color: #333;
                    color: #ffffff;
                }

                .bubble {
                    position: absolute;
                    border-radius: 50%;
                    background-color: rgba(255, 255, 255, 0.3);
                }

                .bubble-1 {
                    width: 150px;
                    height: 150px;
                    top: 10%;
                    left: 10%;
                }

                .bubble-2 {
                    width: 80px;
                    height: 80px;
                    top: 50%;
                    right: 15%;
                }

                .bubble-3 {
                    width: 120px;
                    height: 120px;
                    bottom: 10%;
                    left: 20%;
                }

                .bubble-4 {
                    width: 60px;
                    height: 60px;
                    top: 20%;
                    right: 20%;
                }

                /* Responsive styling */
                @media (max-width: 768px) {
                    body {
                        flex-direction: column;
                    }

                    .left-panel, .right-panel {
                        width: 100%;
                    }

                    .left-panel {
                        height: 60vh;
                    }

                    .right-panel {
                        height: 40vh;
                    }
                }

                /* Dark mode support */
                @media (prefers-color-scheme: dark) {
                    .left-panel {
                        background: linear-gradient(135deg, #ff8c00 0%, #8a4e00 100%);
                    }

                    h1 {
                        color: #f0f0f0;
                    }

                    .nav-link {
                        background-color: #2a2a2a;
                        color: #f0f0f0;
                    }

                    .nav-link:hover {
                        background-color: #f0f0f0;
                        color: #2a2a2a;
                    }
                }
            </style>
        @endif
    </head>
    <body>
        <div class="left-panel">
            <!-- Decorative bubbles -->
            <div class="bubble bubble-1"></div>
            <div class="bubble bubble-2"></div>
            <div class="bubble bubble-3"></div>
            <div class="bubble bubble-4"></div>

            <h1 class="text-5xl font-semibold mb-6">Laravelへようこそ</h1>

            @if (Route::has('login'))
                <div class="nav-links">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="nav-link">ダッシュボード</a>
                    @else
                        <a href="{{ route('login') }}" class="nav-link">ログイン</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="nav-link">新規登録</a>
                        @endif
                    @endauth

                    <a href="{{ route('events.index') }}" class="nav-link">イベントカレンダー</a>
                    <a href="https://laravel.com/docs" target="_blank" class="nav-link">ドキュメント</a>
                    <a href="https://laracasts.com" target="_blank" class="nav-link">Laracasts</a>
                    <a href="https://cloud.laravel.com" target="_blank" class="nav-link">デプロイ</a>
                </div>
            @endif
        </div>

        <div class="right-panel"
             style="background-image: url('{{ asset('images/background.webp') }}');"
             role="img"
             aria-label="背景画像">
            <!-- Background image - keeps the space if image fails to load -->
        </div>
    </body>
</html>
