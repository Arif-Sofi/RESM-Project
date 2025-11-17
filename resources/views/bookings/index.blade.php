<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-base leading-tight">
            {{ __('navigation.bookings') }}
        </h2>
    </x-slot>

    <div class="w-full py-6">
        <div class="w-full max-w-[1920px] mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Set data for Alpine component -->
            <script>
                window.bookingRoomsData = @json($rooms);
                window.bookingAuthUserId = {{ Auth::id() }};
            </script>

            <!-- Unified Booking Interface -->
            <div x-data="unifiedBooking(window.bookingRoomsData, window.bookingAuthUserId)" class="space-y-6">

                <!-- Top Action Bar -->
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4">
                    <div class="flex gap-2">
                        <a href="{{ route('bookings.my-bookings') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-200 uppercase tracking-widest hover:bg-gray-50 dark:hover:bg-gray-600 transition">
                            {{ __('My Bookings') }}
                        </a>
                        @can('viewAny', App\Models\Booking::class)
                        <a href="{{ route('admin.approvals') }}" class="inline-flex items-center px-4 py-2 bg-primary dark:bg-primary border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-opacity-80 transition">
                            {{ __('Pending Approvals') }}
                            @if($pendingCount = App\Models\Booking::whereNull('status')->count())
                            <span class="ml-2 px-2 py-0.5 bg-red-500 text-white rounded-full text-xs">{{ $pendingCount }}</span>
                            @endif
                        </a>
                        @endcan
                    </div>

                    <div class="flex gap-2">
                        <button @click="toggleView('calendar')" :class="currentView === 'calendar' ? 'bg-primary text-white' : 'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200'" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs uppercase tracking-widest hover:bg-opacity-80 transition">
                            {{ __('Calendar View') }}
                        </button>
                        <button @click="toggleView('list')" :class="currentView === 'list' ? 'bg-primary text-white' : 'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200'" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs uppercase tracking-widest hover:bg-opacity-80 transition">
                            {{ __('List View') }}
                        </button>
                    </div>
                </div>

                <!-- Calendar View -->
                <div x-show="currentView === 'calendar'" class="grid grid-cols-1 lg:grid-cols-4 gap-6">

                    <!-- Left Sidebar - Room List -->
                    <div class="lg:col-span-1 space-y-4">
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4">
                            <h3 class="font-semibold text-lg text-gray-800 dark:text-gray-200 mb-4">{{ __('Rooms') }}</h3>

                            <!-- Room Search -->
                            <div class="mb-4">
                                <input type="text" x-model="roomSearch" placeholder="{{ __('Search rooms...') }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm dark:bg-gray-700 dark:text-gray-200 focus:ring-primary focus:border-primary">
                            </div>

                            <!-- Show All Rooms Button -->
                            <button @click="selectedRoom = null; loadCalendarEvents()" class="w-full mb-2 px-3 py-2 text-left rounded-md text-sm font-medium transition" :class="selectedRoom === null ? 'bg-primary text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600'">
                                <span class="block font-semibold">{{ __('All Rooms') }}</span>
                            </button>

                            <!-- Room List -->
                            <div class="space-y-2 max-h-[500px] overflow-y-auto">
                                <template x-for="room in filteredRooms" :key="room.id">
                                    <button @click="selectRoom(room)" class="w-full px-3 py-3 text-left rounded-md text-sm transition hover:shadow-md" :class="selectedRoom?.id === room.id ? 'bg-primary text-white shadow-md' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600'">
                                        <span class="block font-semibold" x-text="room.name"></span>
                                        <span class="block text-xs opacity-75" x-text="room.location_details"></span>
                                    </button>
                                </template>
                            </div>
                        </div>

                        <!-- Legend -->
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4">
                            <h4 class="font-semibold text-sm text-gray-800 dark:text-gray-200 mb-3">{{ __('Status Legend') }}</h4>
                            <div class="space-y-2 text-xs">
                                <div class="flex items-center gap-2">
                                    <span class="w-4 h-4 rounded bg-green-500"></span>
                                    <span class="text-gray-700 dark:text-gray-300">{{ __('Approved') }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="w-4 h-4 rounded bg-yellow-500"></span>
                                    <span class="text-gray-700 dark:text-gray-300">{{ __('Pending') }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="w-4 h-4 rounded bg-red-500"></span>
                                    <span class="text-gray-700 dark:text-gray-300">{{ __('Rejected') }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="w-4 h-4 rounded border-2 border-blue-500 bg-transparent"></span>
                                    <span class="text-gray-700 dark:text-gray-300">{{ __('Your Booking') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Side - Calendar and Booking Form -->
                    <div class="lg:col-span-3 space-y-6">

                        <!-- Calendar Container -->
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4">
                            <div id="calendar" class="min-h-[600px]"></div>
                        </div>

                        <!-- Booking Form (shows when date/room selected) -->
                        <div x-show="showBookingForm" x-transition class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                            @include('bookings._booking_form')
                        </div>

                        <!-- Available Rooms (shows when time slot selected) -->
                        <div x-show="selectedDate && !selectedRoom && availableRooms.length > 0" x-transition class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                            <h3 class="font-semibold text-lg text-gray-800 dark:text-gray-200 mb-4">
                                {{ __('Available Rooms for Selected Time') }}
                            </h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <template x-for="room in availableRooms" :key="room.id">
                                    <button @click="selectRoomForBooking(room)" class="p-4 text-left border-2 border-gray-200 dark:border-gray-600 rounded-lg hover:border-primary hover:shadow-md transition">
                                        <div class="font-semibold text-gray-800 dark:text-gray-200" x-text="room.name"></div>
                                        <div class="text-sm text-gray-600 dark:text-gray-400" x-text="room.location_details"></div>
                                        <div class="text-xs text-green-600 dark:text-green-400 mt-1">✓ {{ __('Available') }}</div>
                                    </button>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- List View -->
                <div x-show="currentView === 'list'" class="bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                    @include('bookings._table_list')
                </div>
            </div>
        </div>
    </div>

    <!-- Include FullCalendar -->
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js'></script>
</x-app-layout>
