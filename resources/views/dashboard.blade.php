<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-base leading-tight">
            {{ __('dashboard.dashboard') }}
        </h2>
    </x-slot>

    <div class="w-full py-6">
        <div class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Welcome Section -->
            <div class="mb-6 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="font-bold text-2xl text-gray-900 dark:text-gray-100 mb-2">
                        {{ __('dashboard.welcome') }}, {{ Auth::user()->name }}!
                    </h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-4">
                        {{ __('dashboard.overtime_message') }}
                    </p>
                    <div class="flex gap-3">
                        <a href="{{ route('bookings.index') }}"
                            class="inline-flex items-center px-6 py-3 bg-primary border border-transparent rounded-md font-semibold text-sm text-white hover:bg-opacity-90 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 transition">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            {{ __('Create New Booking') }}
                        </a>
                        <a href="{{ route('bookings.my-bookings') }}"
                            class="inline-flex items-center px-6 py-3 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 transition">
                            {{ __('View All My Bookings') }}
                        </a>
                    </div>
                </div>
            </div>

            <!-- Admin Quick Actions -->
            @if(auth()->user()->isAdmin())
            <div class="mb-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                <a href="{{ route('admin.approvals') }}" class="block bg-gradient-to-r from-yellow-400 to-yellow-500 rounded-lg shadow-md hover:shadow-lg transition p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="text-white font-bold text-lg mb-1">{{ __('Pending Approvals') }}</h4>
                            <p class="text-yellow-100 text-sm">{{ __('Review booking requests') }}</p>
                        </div>
                        <div class="flex items-center justify-center w-16 h-16 bg-white bg-opacity-30 rounded-full">
                            <span class="text-3xl font-bold text-white">{{ App\Models\Booking::whereNull('status')->count() }}</span>
                        </div>
                    </div>
                </a>
                <a href="{{ route('bookings.index') }}" class="block bg-gradient-to-r from-blue-400 to-blue-500 rounded-lg shadow-md hover:shadow-lg transition p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="text-white font-bold text-lg mb-1">{{ __('All Bookings') }}</h4>
                            <p class="text-blue-100 text-sm">{{ __('View calendar and manage bookings') }}</p>
                        </div>
                        <div class="flex items-center justify-center w-16 h-16 bg-white bg-opacity-30 rounded-full">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    </div>
                </a>
            </div>
            @endif

            <!-- Upcoming Bookings -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="font-bold text-xl text-gray-900 dark:text-gray-100">
                            {{ __('Your Upcoming Bookings') }}
                        </h3>
                        @if($bookings->isNotEmpty())
                        <a href="{{ route('bookings.my-bookings') }}" class="text-sm text-primary hover:text-primary-dark dark:hover:text-primary-light font-medium">
                            {{ __('View All') }} →
                        </a>
                        @endif
                    </div>

                    @if($bookings->isEmpty())
                        <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-12 text-center">
                            <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">
                                {{ __('dashboard.no_bookings_set') }}
                            </h4>
                            <p class="text-gray-600 dark:text-gray-400 mb-4">
                                {{ __('Get started by creating your first room booking') }}
                            </p>
                            <a href="{{ route('bookings.index') }}"
                                class="inline-flex items-center px-6 py-3 bg-primary border border-transparent rounded-md font-semibold text-sm text-white hover:bg-opacity-90 transition">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                {{ __('dashboard.create_booking') }}
                            </a>
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach($bookings as $booking)
                            <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-5 hover:shadow-md transition">
                                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                                    <div class="flex-1">
                                        <div class="flex items-start gap-4">
                                            <div class="flex-shrink-0">
                                                <div class="w-12 h-12 rounded-lg flex items-center justify-center
                                                    @if($booking->status === null) bg-yellow-100 dark:bg-yellow-900/30
                                                    @elseif($booking->status === true) bg-green-100 dark:bg-green-900/30
                                                    @else bg-red-100 dark:bg-red-900/30
                                                    @endif">
                                                    <svg class="w-6 h-6
                                                        @if($booking->status === null) text-yellow-600 dark:text-yellow-400
                                                        @elseif($booking->status === true) text-green-600 dark:text-green-400
                                                        @else text-red-600 dark:text-red-400
                                                        @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="flex-1">
                                                <div class="flex items-center gap-2 mb-1">
                                                    <h4 class="font-semibold text-lg text-gray-900 dark:text-gray-100">{{ $booking->room->name }}</h4>
                                                    @if($booking->status === null)
                                                        <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100">
                                                            {{ __('Pending') }}
                                                        </span>
                                                    @elseif($booking->status === true)
                                                        <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100">
                                                            {{ __('Approved') }}
                                                        </span>
                                                    @else
                                                        <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100">
                                                            {{ __('Rejected') }}
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="flex flex-wrap gap-4 text-sm text-gray-600 dark:text-gray-400">
                                                    <div class="flex items-center">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                        </svg>
                                                        {{ $booking->start_time->format('M d, Y') }}
                                                    </div>
                                                    <div class="flex items-center">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                        </svg>
                                                        {{ $booking->start_time->format('g:i A') }} - {{ $booking->end_time->format('g:i A') }}
                                                    </div>
                                                    <div class="flex items-center">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                                        </svg>
                                                        {{ $booking->number_of_student }} {{ __('students') }}
                                                    </div>
                                                </div>
                                                <p class="mt-2 text-sm text-gray-700 dark:text-gray-300 line-clamp-1">
                                                    {{ $booking->purpose }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex md:flex-col gap-2">
                                        @if($booking->status === null)
                                            <a href="{{ route('bookings.edit', $booking) }}" class="px-4 py-2 text-sm bg-blue-600 hover:bg-blue-700 text-white rounded-md transition text-center">
                                                {{ __('Edit') }}
                                            </a>
                                        @endif
                                        @if(auth()->user()->isAdmin())
                                            @include('_approve-disapprove', ['booking' => $booking])
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
