<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('dashboard.dashboard') }}
        </h2>
    </x-slot>

    <div class="w-full py-12">
        <div class="w-full max-w-[1920px] mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Welcome Card -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg col-span-1 md:col-span-2">
                    <div class="p-6">
                        <h3 class="font-semibold text-lg text-gray-800 dark:text-gray-200 mb-4">
                            {{ __('dashboard.welcome') }} {{ Auth::user()->name }} {{ __('dashboard.honorific') }}
                        </h3>
                        <p class="text-gray-600 dark:text-gray-400">
                            {{ __('dashboard.overtime_message') }}
                        </p>
                    </div>
                </div>

                <!-- Quick Links Card -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="font-semibold text-lg text-gray-800 dark:text-gray-200 mb-4">
                            {{ __('dashboard.quick_links') }}
                        </h3>
                        <div class="space-y-3">
                            <a href="{{ route('bookings.index') }}"
                                class="block p-3 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition">
                                <span class="text-gray-700 dark:text-gray-300">{{ __('dashboard.bookings') }}</span>
                            </a>
                            <a href="{{ route('events.index') }}"
                                class="block p-3 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition">
                                <span
                                    class="text-gray-700 dark:text-gray-300">{{ __('dashboard.event_calendar') }}</span>
                            </a>
                            <a href="{{ route('profile.edit') }}"
                                class="block p-3 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition">
                                <span
                                    class="text-gray-700 dark:text-gray-300">{{ __('dashboard.profile_settings') }}</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- User's Booked Events Card -->
            <div class="mt-6 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- Booked Rooms list -->
                    @if ($bookings->isEmpty())
                        <h3 class="font-semibold text-lg text-gray-800 dark:text-gray-200 mb-4">
                            {{ __('Booked Rooms') }}
                        </h3>
                        <div
                            class="border border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-6 flex flex-col items-center justify-center">
                            <p class="text-gray-500 dark:text-gray-400 text-center">
                                {{ __('dashboard.no_bookings_set') }}
                            </p>
                            <a href="{{ route('bookings.index') }}"
                                class="mt-3 inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                {{ __('dashboard.create_booking') }}
                            </a>
                        </div>
                    @else
                        <div x-data="{ open: true }">
                            <button @click="open = !open">
                                <h3 class="font-semibold text-lg text-gray-800 dark:text-gray-200 mb-4">
                                    {{ __('Booked Rooms') }}
                                </h3>
                            </button>
                            <div x-show="open" x-collapse>
                                <div class="space-y-4">
                                    @foreach ($bookings as $booking)
                                        <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg shadow">
                                            <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">
                                                {{ $booking->room->name }}
                                            </p>
                                            <p class="text-xs text-gray-600 dark:text-gray-400">
                                                {{ $booking->start_time->format('Y-m-d H:i') }} -
                                                {{ $booking->end_time->format('H:i') }}
                                            </p>
                                            <p class="text-sm text-gray-700 dark:text-gray-300 mt-2">
                                                Purpose: {{ $booking->purpose ?? 'No purpose' }}
                                            </p>
                                            <p class="text-sm text-gray-700 dark:text-gray-300 mt-2">
                                                No of student: {{ $booking->number_of_student }}
                                            </p>
                                            <p class="text-sm text-gray-700 dark:text-gray-300 mt-2">
                                                Equipment needed:
                                                {{ $booking->equipment_needed ?? 'No equipment needed' }}
                                            </p>
                                            <p class="text-sm text-gray-700 dark:text-gray-300 mt-2">
                                                Status:
                                                {{-- $booking->status === null ? 'Pending' : ($booking->status ? 'Approved' : 'Disapproved') --}}
                                                @if ($booking->status === null)
                                                    <span class="text-yellow-500 dark:text-yellow-400">
                                                        {{ __('Pending') }}
                                                    </span>
                                                @elseif ($booking->status)
                                                    <span class="text-green-500 dark:text-green-400">
                                                        {{ __('Approved') }}
                                                    </span>
                                                @else
                                                    <span class="text-red-500 dark:text-red-400">
                                                        {{ __('Disapproved') }}
                                                    </span>
                                                @endif
                                            </p>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>


            <!-- Upcoming Events Card -->
            <div class="mt-6 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="font-semibold text-lg text-gray-800 dark:text-gray-200 mb-4">
                        {{ __('dashboard.upcoming_events') }}
                    </h3>

                    <!-- Placeholder for upcoming events list -->
                    <div
                        class="border border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-6 flex flex-col items-center justify-center">
                        <p class="text-gray-500 dark:text-gray-400 text-center">
                            {{ __('dashboard.no_events_set') }}
                        </p>
                        <a href="{{ route('events.index') }}"
                            class="mt-3 inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                            {{ __('dashboard.create_event') }}
                        </a>
                    </div>
                </div>
            </div>

            <!-- System Status Card -->
            <div class="mt-6 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="font-semibold text-lg text-gray-800 dark:text-gray-200 mb-4">
                        {{ __('dashboard.system_info') }}
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('dashboard.app_version') }}</p>
                            <p class="font-medium text-gray-800 dark:text-gray-200">
                                {{ config('app.version', '1.0.0') }}</p>
                        </div>
                        <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('dashboard.user_status') }}</p>
                            <p class="font-medium text-gray-800 dark:text-gray-200">
                                {{ Auth::user()->role ?? __('dashboard.minion') }}</p>
                        </div>
                        <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('dashboard.last_login') }}</p>
                            <p class="font-medium text-gray-800 dark:text-gray-200">{{ now()->format('Y-m-d H:i') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
