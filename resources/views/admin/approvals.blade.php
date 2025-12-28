<x-app-layout>
    <x-slot name="breadcrumb">
        <x-breadcrumb :items="[
            ['label' => __('Pending Approvals'), 'active' => true]
        ]" />
    </x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-base leading-tight">
            {{ __('Pending Approvals') }}
        </h2>
    </x-slot>

    <!-- Pass pending bookings data to JavaScript -->
    <script>
        window.pendingBookingsData = @json($pendingBookings);
        window.appLocale = '{{ app()->getLocale() }}';
        window.localeMap = { 'en': 'en-US', 'ja': 'ja-JP', 'ms': 'ms-MY' };
        window.bookingCountLabel = '{{ __("messages.bookings") }}';
        window.studentsLabel = '{{ __("messages.students") }}';
    </script>

    <div class="w-full py-6" x-data="pendingApprovals()">
        <div class="w-full max-w-[1920px] mx-auto px-4 sm:px-6 lg:px-8">

            <div class="mb-6 flex justify-between items-center">
                <a href="{{ route('bookings.index') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-200 uppercase tracking-widest hover:bg-gray-50 dark:hover:bg-gray-600 transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    {{ __('Back to Calendar') }}
                </a>
            </div>

            <!-- Empty State -->
            <div x-show="bookings.length === 0" class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-12 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">{{ __('All Caught Up!') }}</h3>
                <p class="text-gray-600 dark:text-gray-400">{{ __('There are no pending bookings to review at the moment.') }}</p>
            </div>

            <div x-show="bookings.length > 0">
                <!-- Pending Count & Filters -->
                <div class="mb-6 bg-yellow-50 dark:bg-yellow-900/20 border-l-4 border-yellow-500 rounded-lg p-4">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            <div>
                                <h3 class="text-lg font-semibold text-yellow-800 dark:text-yellow-200">
                                    <span x-text="filteredBookings.length"></span> {{ __('Pending Booking') }}<span x-show="filteredBookings.length !== 1">s</span>
                                </h3>
                                <p class="text-sm text-yellow-700 dark:text-yellow-300">{{ __('Review and approve or reject the bookings below') }}</p>
                            </div>
                        </div>

                        <!-- Month Filter Dropdown -->
                        <div class="flex items-center gap-2">
                            <label class="text-sm text-yellow-700 dark:text-yellow-300 font-medium">{{ __('messages.month') }}:</label>
                            <select x-model="monthFilter" class="px-3 py-1.5 text-sm border border-yellow-300 dark:border-yellow-600 rounded-md bg-white dark:bg-gray-700 dark:text-gray-200 focus:ring-primary focus:border-primary">
                                <option value="all">{{ __('messages.all') }}</option>
                                <template x-for="option in monthOptions" :key="option.value">
                                    <option :value="option.value" x-text="option.label"></option>
                                </template>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Pending Bookings List - Grouped by Month -->
                <div class="space-y-4">
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
                                    <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-yellow-200 dark:bg-yellow-800 text-yellow-800 dark:text-yellow-200"
                                        x-text="`${group.bookings.length} pending`"></span>
                                </div>
                            </button>

                            <!-- Bookings in this month -->
                            <div x-show="isMonthExpanded(group.key)" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="divide-y divide-gray-200 dark:divide-gray-700">
                                <template x-for="booking in group.bookings" :key="booking.id">
                                    <div class="p-6 bg-white dark:bg-gray-800">
                                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                                            <!-- Booking Info -->
                                            <div class="flex-1">
                                                <div class="flex items-start gap-4">
                                                    <div class="flex-shrink-0">
                                                        <div class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg flex items-center justify-center">
                                                            <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                            </svg>
                                                        </div>
                                                    </div>
                                                    <div class="flex-1">
                                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100" x-text="booking.room?.name"></h3>
                                                        <p class="text-sm text-gray-600 dark:text-gray-400" x-text="booking.room?.location_details"></p>
                                                        <div class="mt-2 flex flex-wrap gap-4 text-sm">
                                                            <div class="flex items-center text-gray-700 dark:text-gray-300">
                                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                                                </svg>
                                                                <span x-text="booking.user?.name"></span>
                                                            </div>
                                                            <div class="flex items-center text-gray-700 dark:text-gray-300">
                                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                                </svg>
                                                                <span x-text="formatDate(booking.start_time)"></span>
                                                            </div>
                                                            <div class="flex items-center text-gray-700 dark:text-gray-300">
                                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                                </svg>
                                                                <span x-text="`${formatTime(booking.start_time)} - ${formatTime(booking.end_time)}`"></span>
                                                            </div>
                                                            <div class="flex items-center text-gray-700 dark:text-gray-300">
                                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                                                </svg>
                                                                <span x-text="`${booking.number_of_student} {{ __('students') }}`"></span>
                                                            </div>
                                                        </div>
                                                        <button @click="toggleDetails(booking.id)" class="mt-3 text-sm text-primary hover:text-primary-dark dark:hover:text-primary-light font-medium">
                                                            <span x-text="expandedBooking === booking.id ? '{{ __('Hide Details') }}' : '{{ __('Show Details') }}'"></span>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Action Buttons -->
                                            <div class="flex md:flex-col gap-2 md:min-w-[160px]">
                                                <button @click="openApproveModal(booking.id)" class="flex-1 md:flex-none px-4 py-2.5 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-md transition flex items-center justify-center">
                                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                    </svg>
                                                    {{ __('Approve') }}
                                                </button>
                                                <button @click="openRejectModal(booking.id)" class="flex-1 md:flex-none px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-md transition flex items-center justify-center">
                                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                    </svg>
                                                    {{ __('Reject') }}
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Expanded Details -->
                                        <div x-show="expandedBooking === booking.id" x-transition class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                                            <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                <div>
                                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Purpose') }}</dt>
                                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100" x-text="booking.purpose"></dd>
                                                </div>
                                                <div>
                                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Equipment Needed') }}</dt>
                                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100" x-text="booking.equipment_needed || '{{ __('None') }}'"></dd>
                                                </div>
                                                <div>
                                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('User Email') }}</dt>
                                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100" x-text="booking.user?.email"></dd>
                                                </div>
                                                <div>
                                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Requested On') }}</dt>
                                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100" x-text="formatDateTime(booking.created_at)"></dd>
                                                </div>
                                            </dl>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Reject Modal -->
                <div x-show="showRejectModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                    <div class="flex items-center justify-center min-h-screen px-4">
                        <div @click="closeRejectModal()" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
                        <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full p-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">{{ __('Reject Booking') }}</h3>
                            <form :action="`/bookings/${rejectBookingId}/reject`" method="POST">
                                @csrf
                                <div class="mb-4">
                                    <label for="rejection_reason" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        {{ __('Rejection Reason') }} <span class="text-red-500">*</span>
                                    </label>
                                    <textarea id="rejection_reason" name="rejection_reason" rows="4" required placeholder="{{ __('Please provide a reason for rejection...') }}"
                                        class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-md shadow-sm focus:ring-primary focus:border-primary"></textarea>
                                </div>
                                <div class="flex justify-end gap-3">
                                    <button type="button" @click="closeRejectModal()" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-md hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                                        {{ __('Cancel') }}
                                    </button>
                                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition">
                                        {{ __('Reject Booking') }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Approve Modal -->
                <div x-show="showApproveModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                    <div class="flex items-center justify-center min-h-screen px-4">
                        <div @click="closeApproveModal()" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
                        <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full p-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">{{ __('Approve Booking') }}</h3>
                            <p class="text-gray-600 dark:text-gray-400 mb-6">{{ __('Are you sure you want to approve this booking?') }}</p>
                            <form :action="`/bookings/${approveBookingId}/approve`" method="POST">
                                @csrf
                                <div class="flex justify-end gap-3">
                                    <button type="button" @click="closeApproveModal()" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-md hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                                        {{ __('Cancel') }}
                                    </button>
                                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition">
                                        {{ __('Approve Booking') }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    function pendingApprovals() {
        return {
            // Bookings data
            bookings: window.pendingBookingsData || [],

            // Filter state
            monthFilter: 'all',
            expandedMonths: [],  // Track which months are expanded
            expandedBooking: null,

            // Reject modal state
            showRejectModal: false,
            rejectBookingId: null,

            // Approve modal state
            showApproveModal: false,
            approveBookingId: null,

            // Initialize expanded months (first month expanded by default)
            init() {
                if (this.groupedBookings.length > 0) {
                    this.expandedMonths = [this.groupedBookings[0].key];
                }
            },

            // Filtered bookings getter
            get filteredBookings() {
                let filtered = this.bookings;

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

            // ... (keep other getters)

            // Open reject modal
            openRejectModal(bookingId) {
                this.rejectBookingId = bookingId;
                this.showRejectModal = true;
            },

            // Close reject modal
            closeRejectModal() {
                this.showRejectModal = false;
                this.rejectBookingId = null;
            },

            // Open approve modal
            openApproveModal(bookingId) {
                this.approveBookingId = bookingId;
                this.showApproveModal = true;
            },

            // Close approve modal
            closeApproveModal() {
                this.showApproveModal = false;
                this.approveBookingId = null;
            },
            get monthOptions() {
                const options = [];
                const now = new Date();
                const locale = window.localeMap[window.appLocale] || 'en-US';

                // Get unique months from bookings
                const bookingMonths = new Set();
                this.bookings.forEach(b => {
                    const date = new Date(b.start_time);
                    bookingMonths.add(`${date.getFullYear()}-${date.getMonth() + 1}`);
                });

                // Add next 6 months
                for (let i = 0; i < 6; i++) {
                    const date = new Date(now.getFullYear(), now.getMonth() + i, 1);
                    bookingMonths.add(`${date.getFullYear()}-${date.getMonth() + 1}`);
                }

                // Convert to sorted array
                const sortedMonths = Array.from(bookingMonths).sort((a, b) => {
                    const [aYear, aMonth] = a.split('-').map(Number);
                    const [bYear, bMonth] = b.split('-').map(Number);
                    return (aYear * 12 + aMonth) - (bYear * 12 + bMonth);
                });

                sortedMonths.forEach(key => {
                    const [year, month] = key.split('-').map(Number);
                    const date = new Date(year, month - 1, 1);
                    options.push({
                        value: key,
                        label: date.toLocaleDateString(locale, { year: 'numeric', month: 'long' })
                    });
                });

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

                // Sort groups by date (earliest first for pending approvals)
                return Object.values(groups).sort((a, b) => {
                    const [aYear, aMonth] = a.key.split('-').map(Number);
                    const [bYear, bMonth] = b.key.split('-').map(Number);
                    return (aYear * 12 + aMonth) - (bYear * 12 + bMonth);
                });
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

            // Toggle booking details
            toggleDetails(bookingId) {
                this.expandedBooking = this.expandedBooking === bookingId ? null : bookingId;
            },

            // Open reject modal
            openRejectModal(bookingId) {
                this.rejectBookingId = bookingId;
                this.showRejectModal = true;
            },

            // Close reject modal
            closeRejectModal() {
                this.showRejectModal = false;
                this.rejectBookingId = null;
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

            formatDateTime(datetime) {
                return new Date(datetime).toLocaleDateString('en-US', {
                    month: 'short',
                    day: 'numeric',
                    year: 'numeric',
                    hour: 'numeric',
                    minute: '2-digit',
                    hour12: true
                });
            }
        }
    }
    </script>

    <style>
        [x-cloak] { display: none !important; }
    </style>
</x-app-layout>
