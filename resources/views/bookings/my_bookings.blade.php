<x-app-layout>
    <x-slot name="breadcrumb">
        <x-breadcrumb :items="[
            ['label' => __('My Bookings'), 'active' => true]
        ]" />
    </x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-base leading-tight">
            {{ __('My Bookings') }}
        </h2>
    </x-slot>

    <div class="w-full py-6" x-data="myBookingsList()">
        <div class="w-full max-w-[1920px] mx-auto px-4 sm:px-6 lg:px-8">

            <div class="mb-6 flex justify-between items-center">
                <button onclick="history.back()" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-200 uppercase tracking-widest hover:bg-gray-50 dark:hover:bg-gray-600 transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    {{ __('Back') }}
                </button>
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

            <!-- View Booking Modal -->
            @include('bookings._view_modal')

            <!-- Edit Booking Modal -->
            @include('bookings._edit_modal')
        </div>
    </div>

    <script>
    function myBookingsList() {
        return {
            bookings: @json($bookings),
            rooms: @json($rooms),
            searchQuery: '',
            statusFilter: 'all',
            sortBy: 'date_desc',
            expandedBooking: null,

            // Pagination state
            currentPage: 1,
            pageSize: 20,

            // View modal state
            showViewModal: false,
            viewBookingData: null,

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

            toggleSort(column) {
                const ascKey = column + '_asc';
                const descKey = column + '_desc';
                if (this.sortBy === ascKey) {
                    this.sortBy = descKey;
                } else if (this.sortBy === descKey) {
                    this.sortBy = ascKey;
                } else {
                    // Default to descending for date, ascending for others
                    this.sortBy = column === 'date' ? descKey : ascKey;
                }
            },

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
                    switch (this.sortBy) {
                        case 'date_desc':
                            return new Date(b.start_time) - new Date(a.start_time);
                        case 'date_asc':
                            return new Date(a.start_time) - new Date(b.start_time);
                        case 'room_asc':
                            return (a.room?.name || '').localeCompare(b.room?.name || '');
                        case 'room_desc':
                            return (b.room?.name || '').localeCompare(a.room?.name || '');
                        case 'user_asc':
                            return (a.user?.name || '').localeCompare(b.user?.name || '');
                        case 'user_desc':
                            return (b.user?.name || '').localeCompare(a.user?.name || '');
                        case 'purpose_asc':
                            return (a.purpose || '').localeCompare(b.purpose || '');
                        case 'purpose_desc':
                            return (b.purpose || '').localeCompare(a.purpose || '');
                        case 'status_asc':
                            const statusOrderAsc = { null: 0, true: 1, false: 2 };
                            return (statusOrderAsc[a.status] ?? 0) - (statusOrderAsc[b.status] ?? 0);
                        case 'status_desc':
                            const statusOrderDesc = { null: 0, true: 1, false: 2 };
                            return (statusOrderDesc[b.status] ?? 0) - (statusOrderDesc[a.status] ?? 0);
                        default:
                            return 0;
                    }
                });

                return filtered;
            },

            // Paginated bookings for list view
            get paginatedBookings() {
                const startIndex = (this.currentPage - 1) * this.pageSize;
                return this.filteredBookings.slice(startIndex, startIndex + this.pageSize);
            },

            get totalPages() {
                return Math.ceil(this.filteredBookings.length / this.pageSize);
            },

            nextPage() {
                if (this.currentPage < this.totalPages) {
                    this.currentPage++;
                }
            },

            prevPage() {
                if (this.currentPage > 1) {
                    this.currentPage--;
                }
            },

            resetPagination() {
                this.currentPage = 1;
            },

            toggleDetails(bookingId) {
                const id = parseInt(bookingId);
                this.expandedBooking = this.expandedBooking === id ? null : id;
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
            },

            // View Modal Functions
            openViewModal(booking) {
                this.viewBookingData = booking;
                this.showViewModal = true;
            },

            closeViewModal() {
                this.showViewModal = false;
                this.viewBookingData = null;
            },

            async cancelBooking(bookingId) {
                const confirmed = await window.confirmCancel('this booking');
                if (!confirmed) {
                    return;
                }

                try {
                    const response = await fetch(`/bookings/${bookingId}`, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    });

                    if (response.ok) {
                        // Remove the booking from the local list
                        this.bookings = this.bookings.filter(b => b.id !== bookingId);
                        this.closeViewModal();
                        window.showSuccess('Booking cancelled successfully!');
                    } else {
                        const data = await response.json();
                        window.showError(data.message || 'Failed to cancel booking.');
                    }
                } catch (error) {
                    console.error('Error cancelling booking:', error);
                    window.showError('An error occurred while cancelling the booking.');
                }
            },

            // Edit Modal Functions
            openEditModal(booking) {
                this.editBookingId = booking.id;

                const startDate = new Date(booking.start_time);
                const endDate = new Date(booking.end_time);

                // Helper to format date as YYYY-MM-DD in local timezone
                const formatLocalDate = (date) => {
                    const year = date.getFullYear();
                    const month = String(date.getMonth() + 1).padStart(2, '0');
                    const day = String(date.getDate()).padStart(2, '0');
                    return `${year}-${month}-${day}`;
                };

                // Helper to format time as HH:mm in local timezone
                const formatLocalTime = (date) => {
                    const hours = String(date.getHours()).padStart(2, '0');
                    const minutes = String(date.getMinutes()).padStart(2, '0');
                    return `${hours}:${minutes}`;
                };

                this.editBookingData = {
                    room_id: booking.room_id,
                    date: formatLocalDate(startDate),
                    start_time: formatLocalTime(startDate),
                    end_time: formatLocalTime(endDate),
                    number_of_student: booking.number_of_student,
                    equipment_needed: booking.equipment_needed || '',
                    purpose: booking.purpose || ''
                };

                this.editClashError = '';
                this.editErrors = {};
                this.editGeneralError = '';
                this.showEditModal = true;
            },

            closeEditModal() {
                this.showEditModal = false;
                this.editBookingId = null;
                this.editErrors = {};
                this.editClashError = '';
                this.editGeneralError = '';
                this.isSubmitting = false;
            },

            updateEditTimes() {
                this.editClashError = '';
            },

            async checkEditAvailability() {
                if (!this.editBookingData.room_id || !this.editBookingData.date ||
                    !this.editBookingData.start_time || !this.editBookingData.end_time) {
                    return;
                }

                // Convert local inputs to ISO string (UTC)
                const startLocal = new Date(`${this.editBookingData.date}T${this.editBookingData.start_time}:00`);
                const startTime = startLocal.toISOString();

                const endLocal = new Date(`${this.editBookingData.date}T${this.editBookingData.end_time}:00`);
                // Handle crossing midnight if needed (though simple booking usually same day, safe to just parse)
                if (this.editBookingData.end_time < this.editBookingData.start_time) {
                    endLocal.setDate(endLocal.getDate() + 1);
                }
                const endTime = endLocal.toISOString();

                try {
                    const response = await fetch('/api/bookings/check-availability', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            room_id: this.editBookingData.room_id,
                            start_time: startTime,
                            end_time: endTime,
                            booking_id: this.editBookingId
                        })
                    });
                    const data = await response.json();
                    this.editClashError = data.available ? '' : data.message;
                } catch (error) {
                    console.error('Error checking availability:', error);
                }
            },

            async updateBooking() {
                if (this.editClashError || this.isSubmitting) return;

                this.isSubmitting = true;
                this.editErrors = {};
                this.editGeneralError = '';

                // Convert local inputs to ISO string (UTC)
                const startLocal = new Date(`${this.editBookingData.date}T${this.editBookingData.start_time}:00`);
                const startTime = startLocal.toISOString();

                const endLocal = new Date(`${this.editBookingData.date}T${this.editBookingData.end_time}:00`);
                if (this.editBookingData.end_time < this.editBookingData.start_time) {
                    endLocal.setDate(endLocal.getDate() + 1);
                }
                const endTime = endLocal.toISOString();

                try {
                    const response = await fetch(`/bookings/${this.editBookingId}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            room_id: this.editBookingData.room_id,
                            start_time: startTime,
                            end_time: endTime,
                            number_of_student: this.editBookingData.number_of_student,
                            equipment_needed: this.editBookingData.equipment_needed,
                            purpose: this.editBookingData.purpose
                        })
                    });

                    const data = await response.json();

                    if (response.ok && data.success) {
                        // Update the booking in the local list
                        const index = this.bookings.findIndex(b => b.id === this.editBookingId);
                        if (index !== -1) {
                            this.bookings[index] = data.booking;
                        }
                        this.closeEditModal();
                        window.showSuccess(data.message || 'Booking updated successfully!');
                    } else {
                        if (data.errors) {
                            this.editErrors = data.errors;
                        } else {
                            this.editGeneralError = data.message || 'An error occurred while updating the booking.';
                        }
                    }
                } catch (error) {
                    console.error('Error updating booking:', error);
                    this.editGeneralError = 'An error occurred while updating the booking.';
                } finally {
                    this.isSubmitting = false;
                }
            },

            async deleteBooking() {
                if (!this.editBookingId) return;

                const confirmed = await window.confirmDelete('this booking');
                if (!confirmed) {
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
                        // Remove the booking from the local list
                        this.bookings = this.bookings.filter(b => b.id !== this.editBookingId);
                        this.closeEditModal();
                        window.showSuccess('Booking deleted successfully!');
                    } else {
                        const data = await response.json();
                        this.editGeneralError = data.message || 'Failed to delete booking.';
                    }
                } catch (error) {
                    console.error('Error deleting booking:', error);
                    this.editGeneralError = 'An error occurred while deleting the booking.';
                } finally {
                    this.isSubmitting = false;
                }
            }
        }
    }
    </script>
</x-app-layout>
