<x-app-layout>
    <x-slot name="breadcrumb">
        <x-breadcrumb :items="[]" />
    </x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-base leading-tight">
            {{ __('dashboard.dashboard') }}
        </h2>
    </x-slot>

    <!-- Pass bookings data to JavaScript -->
    <script>
        window.dashboardBookingsData = @json($bookings);
        window.dashboardRoomsData = @json($rooms);
        window.appLocale = '{{ app()->getLocale() }}';
        window.localeMap = { 'en': 'en-US', 'ja': 'ja-JP', 'ms': 'ms-MY' };
        window.bookingCountLabel = '{{ __("messages.bookings") }}';
        window.studentsLabel = '{{ __("messages.students") }}';
    </script>

    <div class="w-full py-6" x-data="dashboardBookings()">
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
                            {{ __('messages.create_new_booking') }}
                        </a>
                        <a href="{{ route('bookings.my-bookings') }}"
                            class="inline-flex items-center px-6 py-3 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 transition">
                            {{ __('messages.view_all_my_bookings') }}
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
                            <h4 class="text-white font-bold text-lg mb-1">{{ __('messages.pending_approvals') }}</h4>
                            <p class="text-yellow-100 text-sm">{{ __('messages.review_booking_requests') }}</p>
                        </div>
                        <div class="flex items-center justify-center w-16 h-16 bg-white bg-opacity-30 rounded-full">
                            <span class="text-3xl font-bold text-white">{{ App\Models\Booking::whereNull('status')->count() }}</span>
                        </div>
                    </div>
                </a>
                <a href="{{ route('bookings.index') }}" class="block bg-gradient-to-r from-blue-400 to-blue-500 rounded-lg shadow-md hover:shadow-lg transition p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="text-white font-bold text-lg mb-1">{{ __('messages.all_bookings') }}</h4>
                            <p class="text-blue-100 text-sm">{{ __('messages.view_calendar_and_manage') }}</p>
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
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-bold text-xl text-gray-900 dark:text-gray-100">
                            {{ __('messages.your_upcoming_bookings') }}
                        </h3>
                        <a href="{{ route('bookings.my-bookings') }}" class="text-sm text-primary hover:text-primary-dark dark:hover:text-primary-light font-medium">
                            {{ __('messages.view_all') }} →
                        </a>
                    </div>

                    <!-- Filters Row -->
                    <div class="flex flex-wrap items-center gap-4 mb-6 pb-4 border-b border-gray-200 dark:border-gray-700">
                        <!-- Show/Hide Rejected Toggle -->
                        <div class="flex items-center gap-3">
                            <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('messages.show_rejected') }}:</span>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" x-model="showRejected" class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-primary rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:bg-primary after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all"></div>
                            </label>
                        </div>

                        <!-- Month Filter Dropdown -->
                        <div class="flex items-center gap-2">
                            <label class="text-sm text-gray-600 dark:text-gray-400">{{ __('messages.month') }}:</label>
                            <select x-model="monthFilter" class="px-3 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-gray-200 focus:ring-primary focus:border-primary">
                                <option value="all">{{ __('messages.all') }}</option>
                                <template x-for="option in monthOptions" :key="option.value">
                                    <option :value="option.value" x-text="option.label"></option>
                                </template>
                            </select>
                        </div>

                        <!-- Results count -->
                        <div class="text-sm text-gray-500 dark:text-gray-400 ml-auto">
                            <span x-text="`${filteredBookings.length} ${filteredBookings.length === 1 ? 'booking' : 'bookings'}`"></span>
                        </div>
                    </div>

                    <!-- Empty State -->
                    <div x-show="filteredBookings.length === 0" x-transition class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-12 text-center">
                        <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">
                            {{ __('dashboard.no_bookings_set') }}
                        </h4>
                        <p class="text-gray-600 dark:text-gray-400 mb-4">
                            {{ __('messages.get_started_booking') }}
                        </p>
                        <a href="{{ route('bookings.index') }}"
                            class="inline-flex items-center px-6 py-3 bg-primary border border-transparent rounded-md font-semibold text-sm text-white hover:bg-opacity-90 transition">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            {{ __('dashboard.create_booking') }}
                        </a>
                    </div>

                    <!-- Bookings List - Grouped by Month -->
                    <div x-show="filteredBookings.length > 0" class="space-y-4">
                        <template x-for="group in groupedBookings" :key="group.key">
                            <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                                <!-- Month Header (Collapsible) -->
                                <button @click="toggleMonth(group.key)"
                                    class="w-full flex items-center justify-between px-4 py-3 bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 transition">
                                    <div class="flex items-center gap-3">
                                        <svg class="w-5 h-5 text-gray-500 dark:text-gray-400 transition-transform"
                                            :class="{ 'rotate-90': isMonthExpanded(group.key) }"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                        </svg>
                                        <span class="font-semibold text-gray-800 dark:text-gray-200" x-text="group.label"></span>
                                        <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-gray-200 dark:bg-gray-600 text-gray-600 dark:text-gray-300"
                                            x-text="`${group.bookings.length} ${window.bookingCountLabel}`"></span>
                                    </div>
                                </button>

                                <!-- Bookings in this month -->
                                <div x-show="isMonthExpanded(group.key)" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="divide-y divide-gray-200 dark:divide-gray-700">
                                    <template x-for="booking in group.bookings" :key="booking.id">
                                        <div class="p-5 hover:bg-gray-50 dark:hover:bg-gray-750 transition">
                                            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                                                <div class="flex-1">
                                                    <div class="flex items-start gap-4">
                                                        <div class="flex-shrink-0">
                                                            <div class="w-12 h-12 rounded-lg flex items-center justify-center"
                                                                :class="{
                                                                    'bg-yellow-100 dark:bg-yellow-900/30': booking.status === null,
                                                                    'bg-green-100 dark:bg-green-900/30': booking.status === true,
                                                                    'bg-red-100 dark:bg-red-900/30': booking.status === false
                                                                }">
                                                                <svg class="w-6 h-6"
                                                                    :class="{
                                                                        'text-yellow-600 dark:text-yellow-400': booking.status === null,
                                                                        'text-green-600 dark:text-green-400': booking.status === true,
                                                                        'text-red-600 dark:text-red-400': booking.status === false
                                                                    }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                                </svg>
                                                            </div>
                                                        </div>
                                                        <div class="flex-1">
                                                            <div class="flex items-center gap-2 mb-1">
                                                                <h4 class="font-semibold text-lg text-gray-900 dark:text-gray-100" x-text="booking.room?.name || 'Unknown Room'"></h4>
                                                                <span x-show="booking.status === null" class="px-2 py-0.5 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100">
                                                                    {{ __('messages.pending') }}
                                                                </span>
                                                                <span x-show="booking.status === true" class="px-2 py-0.5 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100">
                                                                    {{ __('messages.approved') }}
                                                                </span>
                                                                <span x-show="booking.status === false" class="px-2 py-0.5 text-xs font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100">
                                                                    {{ __('messages.rejected') }}
                                                                </span>
                                                            </div>
                                                            <div class="flex flex-wrap gap-4 text-sm text-gray-600 dark:text-gray-400">
                                                                <div class="flex items-center">
                                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                                    </svg>
                                                                    <span x-text="formatDate(booking.start_time)"></span>
                                                                </div>
                                                                <div class="flex items-center">
                                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                                    </svg>
                                                                    <span x-text="`${formatTime(booking.start_time)} - ${formatTime(booking.end_time)}`"></span>
                                                                </div>
                                                                <div class="flex items-center">
                                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                                                    </svg>
                                                                    <span x-text="`${booking.number_of_student} ${window.studentsLabel}`"></span>
                                                                </div>
                                                            </div>
                                                            <p class="mt-2 text-sm text-gray-700 dark:text-gray-300 line-clamp-1" x-text="booking.purpose"></p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="flex md:flex-col gap-2">
                                                    <template x-if="booking.status === null">
                                                        <button @click="openEditModal(booking)" class="px-4 py-2 text-sm bg-blue-600 hover:bg-blue-700 text-white rounded-md transition text-center">
                                                            {{ __('messages.edit') }}
                                                        </button>
                                                    </template>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Edit Modal -->
            @include('bookings._edit_modal')
        </div>
    </div>

    <script>
    function dashboardBookings() {
        return {
            // Bookings data
            bookings: window.dashboardBookingsData || [],
            rooms: window.dashboardRoomsData || [],

            // Filter state
            showRejected: false,
            monthFilter: 'all',
            expandedMonths: [],  // Track which months are expanded

            // Edit modal state
            showEditModal: false,
            editBookingId: null,
            editBookingData: {
                room_id: '',
                date: '',
                start_time: '',
                end_time: '',
                number_of_student: '',
                equipment_needed: '',
                purpose: ''
            },
            editClashError: '',
            editErrors: {},
            editGeneralError: '',
            isSubmitting: false,

            // Filtered bookings getter
            get filteredBookings() {
                let filtered = this.bookings;

                // Filter by rejected status
                if (!this.showRejected) {
                    filtered = filtered.filter(b => b.status !== false);
                }

                // Filter by month
                if (this.monthFilter !== 'all') {
                    filtered = filtered.filter(b => {
                        const bookingDate = new Date(b.start_time);
                        const [year, month] = this.monthFilter.split('-');
                        return bookingDate.getFullYear() === parseInt(year) &&
                               bookingDate.getMonth() + 1 === parseInt(month);
                    });
                }

                return filtered;
            },

            // Month options getter (next 6 months)
            get monthOptions() {
                const options = [];
                const now = new Date();
                const locale = window.localeMap[window.appLocale] || 'en-US';
                for (let i = 0; i < 6; i++) {
                    const date = new Date(now.getFullYear(), now.getMonth() + i, 1);
                    options.push({
                        value: `${date.getFullYear()}-${date.getMonth() + 1}`,
                        label: date.toLocaleDateString(locale, { year: 'numeric', month: 'long' })
                    });
                }
                return options;
            },

            // Grouped bookings by month
            get groupedBookings() {
                const groups = {};
                const locale = window.localeMap[window.appLocale] || 'en-US';

                this.filteredBookings.forEach(booking => {
                    const date = new Date(booking.start_time);
                    const key = `${date.getFullYear()}-${date.getMonth() + 1}`;
                    const label = date.toLocaleDateString(locale, { year: 'numeric', month: 'long' });

                    if (!groups[key]) {
                        groups[key] = {
                            key: key,
                            label: label,
                            bookings: []
                        };
                    }
                    groups[key].bookings.push(booking);
                });

                // Sort by date (newest first) and return as array
                return Object.values(groups).sort((a, b) => {
                    const [aYear, aMonth] = a.key.split('-').map(Number);
                    const [bYear, bMonth] = b.key.split('-').map(Number);
                    return (bYear * 12 + bMonth) - (aYear * 12 + aMonth);
                });
            },

            // Initialize expanded months (first month expanded by default)
            init() {
                if (this.groupedBookings.length > 0) {
                    this.expandedMonths = [this.groupedBookings[0].key];
                }
            },

            // Toggle month expansion
            toggleMonth(key) {
                const index = this.expandedMonths.indexOf(key);
                if (index === -1) {
                    this.expandedMonths.push(key);
                } else {
                    this.expandedMonths.splice(index, 1);
                }
            },

            // Check if month is expanded
            isMonthExpanded(key) {
                return this.expandedMonths.includes(key);
            },

            // Date formatting helpers
            formatDate(datetime) {
                return new Date(datetime).toLocaleDateString('en-US', {
                    month: 'short',
                    day: 'numeric',
                    year: 'numeric'
                });
            },

            formatTime(datetime) {
                return new Date(datetime).toLocaleTimeString('en-US', {
                    hour: 'numeric',
                    minute: '2-digit',
                    hour12: true
                });
            },

            openEditModal(booking) {
                this.editBookingId = booking.id;

                const startDate = new Date(booking.start_time);
                const endDate = new Date(booking.end_time);

                this.editBookingData = {
                    room_id: booking.room_id,
                    date: startDate.toISOString().split('T')[0],
                    start_time: startDate.toTimeString().slice(0, 5),
                    end_time: endDate.toTimeString().slice(0, 5),
                    number_of_student: booking.number_of_student,
                    equipment_needed: booking.equipment_needed || '',
                    purpose: booking.purpose
                };

                this.editErrors = {};
                this.editClashError = '';
                this.editGeneralError = '';
                this.showEditModal = true;
            },

            closeEditModal() {
                this.showEditModal = false;
                this.editBookingId = null;
                this.editErrors = {};
                this.editClashError = '';
                this.editGeneralError = '';
            },

            async updateBooking() {
                if (this.isSubmitting) return;

                this.isSubmitting = true;
                this.editErrors = {};
                this.editClashError = '';
                this.editGeneralError = '';

                // Prepare datetime values in SQL format
                const startDateTime = new Date(this.editBookingData.date + 'T' + this.editBookingData.start_time + ':00')
                    .toISOString().slice(0, 19).replace('T', ' ');
                const endDateTime = new Date(this.editBookingData.date + 'T' + this.editBookingData.end_time + ':00')
                    .toISOString().slice(0, 19).replace('T', ' ');

                try {
                    const response = await fetch(`/bookings/${this.editBookingId}`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            room_id: this.editBookingData.room_id,
                            start_time: startDateTime,
                            end_time: endDateTime,
                            number_of_student: this.editBookingData.number_of_student,
                            equipment_needed: this.editBookingData.equipment_needed,
                            purpose: this.editBookingData.purpose
                        })
                    });

                    const data = await response.json();

                    if (!response.ok) {
                        if (data.errors) {
                            this.editErrors = data.errors;
                        }
                        if (data.message) {
                            if (data.message.includes('no longer available')) {
                                this.editClashError = data.message;
                            } else {
                                this.editGeneralError = data.message;
                            }
                        }
                        this.isSubmitting = false;
                        return;
                    }

                    // Success - reload the page to show updated data
                    window.location.reload();
                } catch (error) {
                    console.error('Update error:', error);
                    this.editGeneralError = '{{ __("An error occurred while updating the booking.") }}';
                    this.isSubmitting = false;
                }
            },

            async deleteBooking() {
                if (!this.editBookingId) return;

                if (!confirm('{{ __("Are you sure you want to delete this booking? This action cannot be undone.") }}')) {
                    return;
                }

                this.isSubmitting = true;

                try {
                    const response = await fetch(`/bookings/${this.editBookingId}`, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    });

                    if (response.ok || response.redirected) {
                        this.closeEditModal();
                        // Reload the page to show updated data
                        window.location.reload();
                    } else {
                        const data = await response.json();
                        this.editGeneralError = data.message || '{{ __("Failed to delete booking.") }}';
                    }
                } catch (error) {
                    console.error('Error deleting booking:', error);
                    this.editGeneralError = '{{ __("An error occurred while deleting the booking.") }}';
                } finally {
                    this.isSubmitting = false;
                }
            }
        }
    }
    </script>
</x-app-layout>
