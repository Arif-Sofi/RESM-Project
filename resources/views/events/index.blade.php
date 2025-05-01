<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('イベントカレンダー') }}
        </h2>
    </x-slot>

    <div class="w-full py-12">
        <div class="w-full max-w-[1920px] mx-auto px-4 sm:px-6 lg:px-8">
            <div class="calendar-container">
                <div id="calendar"></div>
            </div>
        </div>
    </div>

    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js'></script>
    @include('events._css')
    @include('events._js')
    @include('events._create_event_modal')

</x-app-layout>
