<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('イベントカレンダー') }}
        </h2>
    </x-slot>

    <div class="w-full py-12">
        <div class="w-full max-w-[1920px] mx-auto px-4 sm:px-6 lg:px-8">
            <div class="calendar-container">
                {{-- FullCalendarを表示するためのHTML要素 --}}
                <!-- モーダル表示に必要なCSS/JSを読み込む (例: Bootstrap) -->
                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
                <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
                <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js'></script>

                <div id="calendar"></div>

                @include('events._event_modal')
            </div>
        </div>
    </div>

    @include('events._css')
    @include('events._js')
</x-app-layout>
