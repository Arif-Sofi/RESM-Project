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
            <!-- Session Messages -->
            {{-- @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif --}}

            @if (session('import_errors'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <strong class="font-bold">Import Failed!</strong>
                    <span class="block sm:inline">Please correct the following errors:</span>
                    <ul class="mt-3 list-disc list-inside text-sm">
                        @foreach (session('import_errors') as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Set data for Alpine component -->
            <script>
                window.eventUsersData = @json($users);
                window.eventAuthUserId = {{ Auth::id() }};
                window.eventsData = @json($events);
            </script>

            <!-- Event Calendar Interface -->
            <div x-data="eventCalendar(window.eventUsersData, window.eventAuthUserId, window.eventsData)" class="space-y-6">

                <!-- Top Action Bar -->
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4">
                    <div class="flex gap-2">
                        <a href="{{ route('events.my-events') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-200 uppercase tracking-widest hover:bg-gray-50 dark:hover:bg-gray-600 transition">
                            {{ __('messages.my_events') }}
                        </a>
                        <form id="import-form" action="{{ route('events.import') }}" method="POST" enctype="multipart/form-data" style="display: none;">
                            @csrf
                            <input type="file" name="file" id="import-file-input" onchange="document.getElementById('import-form').submit()" accept=".xlsx, .xls, .csv">
                        </form>
                        @if(auth()->user()->isAdmin())
                            <button type="button" onclick="document.getElementById('import-file-input').click()" class="inline-flex items-center px-4 py-2 bg-primary dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-white dark:text-gray-200 uppercase tracking-widest hover:bg-opacity-80 dark:hover:bg-gray-600 transition">
                                Import Event
                            </button>
                        @endif
                    </div>

                    <div class="flex gap-2">
                        <button @click="currentView = 'calendar'" :class="currentView === 'calendar' ? 'bg-primary text-white' : 'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200'" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs uppercase tracking-widest hover:bg-opacity-80 transition">
                            {{ __('messages.calendar_view') }}
                        </button>
                        <button @click="currentView = 'list'" :class="currentView === 'list' ? 'bg-primary text-white' : 'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200'" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs uppercase tracking-widest hover:bg-opacity-80 transition">
                            {{ __('messages.list_view') }}
                        </button>
                    </div>
                </div>

                <!-- Calendar View -->
                <div x-show="currentView === 'calendar'" x-cloak>
                    <!-- Legend -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 mb-6">
                        <div class="flex flex-wrap gap-4 text-sm">
                            <div class="flex items-center gap-2">
                                <span class="w-4 h-4 rounded bg-blue-500"></span>
                                <span class="text-gray-700 dark:text-gray-300">{{ __('messages.your_events') }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="w-4 h-4 rounded bg-green-500"></span>
                                <span class="text-gray-700 dark:text-gray-300">{{ __('messages.staff_events') }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="w-4 h-4 rounded bg-gray-500"></span>
                                <span class="text-gray-700 dark:text-gray-300">{{ __('Completed Event') }}</span>
                            </div>
                            <p class="text-gray-500 dark:text-gray-400 text-sm ml-auto">{{ __('messages.click_calendar_to_create') }}</p>
                        </div>
                    </div>

                    <!-- Calendar Container -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4">
                        <div id="event-calendar" class="min-h-[600px]"></div>
                    </div>
                </div>

                <!-- List View -->
                <div x-show="currentView === 'list'" x-cloak>
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                        @include('events._table_list')
                    </div>
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
