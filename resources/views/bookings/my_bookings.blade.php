<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-base leading-tight">
            {{ __('My Bookings') }}
        </h2>
    </x-slot>

    <div class="w-full py-6" x-data="myBookingsList()">
        <div class="w-full max-w-[1920px] mx-auto px-4 sm:px-6 lg:px-8">

            <div class="mb-6 flex justify-between items-center">
                <a href="{{ route('bookings.index') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-200 uppercase tracking-widest hover:bg-gray-50 dark:hover:bg-gray-600 transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    {{ __('Back to Calendar') }}
                </a>
                <a href="{{ route('bookings.index') }}" class="inline-flex items-center px-4 py-2 bg-primary border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-opacity-90 transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    {{ __('Create New Booking') }}
                </a>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 border-l-4 border-gray-400">
                    <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $bookings->count() }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">{{ __('Total Bookings') }}</div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 border-l-4 border-yellow-500">
                    <div class="text-2xl font-bold text-yellow-600">{{ $bookings->whereNull('status')->count() }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">{{ __('Pending') }}</div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 border-l-4 border-green-500">
                    <div class="text-2xl font-bold text-green-600">{{ $bookings->whereStrict('status', true)->count() }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">{{ __('Approved') }}</div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 border-l-4 border-red-500">
                    <div class="text-2xl font-bold text-red-600">{{ $bookings->whereStrict('status', false)->count() }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">{{ __('Rejected') }}</div>
                </div>
            </div>

            <!-- Bookings List -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                @include('bookings._table_list')
            </div>
        </div>
    </div>

    <script>
    function myBookingsList() {
        return {
            bookings: @json($bookings),
            searchQuery: '',
            statusFilter: 'all',
            sortBy: 'date_desc',
            expandedBooking: null,

            get filteredBookings() {
                let filtered = this.bookings;

                // Filter by search query
                if (this.searchQuery) {
                    const query = this.searchQuery.toLowerCase();
                    filtered = filtered.filter(booking =>
                        booking.room?.name?.toLowerCase().includes(query) ||
                        booking.user?.name?.toLowerCase().includes(query) ||
                        booking.purpose?.toLowerCase().includes(query)
                    );
                }

                // Filter by status
                if (this.statusFilter !== 'all') {
                    if (this.statusFilter === 'pending') {
                        filtered = filtered.filter(booking => booking.status === null);
                    } else if (this.statusFilter === 'approved') {
                        filtered = filtered.filter(booking => booking.status === true);
                    } else if (this.statusFilter === 'rejected') {
                        filtered = filtered.filter(booking => booking.status === false);
                    }
                }

                // Sort
                filtered = [...filtered].sort((a, b) => {
                    if (this.sortBy === 'date_desc') {
                        return new Date(b.start_time) - new Date(a.start_time);
                    } else if (this.sortBy === 'date_asc') {
                        return new Date(a.start_time) - new Date(b.start_time);
                    } else if (this.sortBy === 'room') {
                        return (a.room?.name || '').localeCompare(b.room?.name || '');
                    } else if (this.sortBy === 'status') {
                        const statusOrder = { null: 0, true: 1, false: 2 };
                        return (statusOrder[a.status] || 0) - (statusOrder[b.status] || 0);
                    }
                    return 0;
                });

                return filtered;
            },

            toggleDetails(bookingId) {
                this.expandedBooking = this.expandedBooking === bookingId ? null : bookingId;
            },

            formatDate(datetime) {
                return new Date(datetime).toLocaleDateString('en-US', {
                    weekday: 'short',
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric'
                });
            },

            formatTime(datetime) {
                return new Date(datetime).toLocaleTimeString('en-US', {
                    hour: '2-digit',
                    minute: '2-digit'
                });
            }
        }
    }
    </script>
</x-app-layout>
