<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - @yield('title', 'イベント')</title> {{-- タイトルセクション --}}

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')

</head>
<body class="font-sans antialiased">
    <main>
        @yield('content')
    </main>

    {{-- <footer> ... </footer> --}}

    {{-- FullCalendar本体のJavaScript --}}
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js'></script>
    @stack('scripts')

</body>
</html>
