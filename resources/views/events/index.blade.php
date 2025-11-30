<x-app-layout>
    <x-slot name="breadcrumb">
        <x-breadcrumb :items="[
            ['label' => __('Event Calendar'), 'active' => true]
        ]" />
    </x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-base leading-tight">
            {{ __('navigation.event calendar') }}
        </h2>
    </x-slot>

    <div class="w-full py-6">
        <div class="w-full max-w-[1920px] mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Set data for Alpine component -->
            <script>
                window.eventUsersData = @json($users);
                window.eventAuthUserId = {{ Auth::id() }};
            </script>

            <!-- Event Calendar Interface -->
            <div x-data="eventCalendar(window.eventUsersData, window.eventAuthUserId)" class="space-y-6">

                <!-- Top Action Bar -->
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">{{ __('messages.event_calendar') }}</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('messages.event_calendar_description') }}</p>
                    </div>

                    <button @click="openCreateModal()" class="inline-flex items-center px-4 py-2 bg-primary border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-opacity-80 transition">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        {{ __('messages.create_event') }}
                    </button>
                </div>

                <!-- Legend -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4">
                    <div class="flex flex-wrap gap-4 text-sm">
                        <div class="flex items-center gap-2">
                            <span class="w-4 h-4 rounded bg-blue-500"></span>
                            <span class="text-gray-700 dark:text-gray-300">{{ __('messages.your_events') }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-4 h-4 rounded bg-green-500"></span>
                            <span class="text-gray-700 dark:text-gray-300">{{ __('messages.staff_events') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Calendar Container -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4">
                    <div id="event-calendar" class="min-h-[600px]"></div>
                </div>

                <!-- Modals -->
                @include('events._create_modal')
                @include('events._edit_modal')
                @include('events._view_modal')
                @include('events._delete_modal')
            </div>
        </div>
    </div>

    <!-- Include FullCalendar -->
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js'></script>
</x-app-layout>
