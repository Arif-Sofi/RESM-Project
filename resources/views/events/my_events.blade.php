<x-app-layout>
    <x-slot name="breadcrumb">
        <x-breadcrumb :items="[
            ['label' => __('messages.my_events'), 'active' => true]
        ]" />
    </x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-base leading-tight">
            {{ __('messages.my_events') }}
        </h2>
    </x-slot>

    <div class="w-full py-6" x-data="myEventsList()">
        <div class="w-full max-w-[1920px] mx-auto px-4 sm:px-6 lg:px-8">

            <div class="mb-6 flex justify-between items-center">
                <button onclick="history.back()" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-200 uppercase tracking-widest hover:bg-gray-50 dark:hover:bg-gray-600 transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    {{ __('messages.back') }}
                </button>
                <a href="{{ route('events.index') }}" class="inline-flex items-center px-4 py-2 bg-primary border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-opacity-90 transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    {{ __('messages.view_calendar') }}
                </a>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 border-l-4 border-blue-500">
                    <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $events->count() }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">{{ __('messages.total_events') }}</div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 border-l-4 border-green-500">
                    <div class="text-2xl font-bold text-green-600">{{ $events->where('start_at', '>=', now())->count() }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">{{ __('messages.upcoming') }}</div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 border-l-4 border-gray-400">
                    <div class="text-2xl font-bold text-gray-600">{{ $events->where('start_at', '<', now())->count() }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">{{ __('messages.past') }}</div>
                </div>
            </div>

            <!-- Events List -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                @include('events._table_list')
            </div>

            <!-- Modals -->
            @include('events._edit_modal')
            @include('events._view_modal')
            @include('events._delete_modal')
        </div>
    </div>

    <script>
    function myEventsList() {
        return {
            events: @json($events),
            users: @json($users),
            authUserId: {{ Auth::id() }},
            searchQuery: '',
            sortBy: 'date_desc',

            // Edit modal state
            showEditModal: false,
            editEventId: null,
            editEventData: {
                title: '',
                description: '',
                date: '',
                start_time: '',
                end_time: '',
                staff: []
            },
            editErrors: {},
            editGeneralError: '',
            isSubmitting: false,
            showEditStaffDropdown: false,

            // View modal state
            showViewModal: false,
            viewEventData: null,

            // Delete modal state
            showDeleteModal: false,
            deleteEventId: null,

            // Filter out current user from staff list
            get availableStaff() {
                return this.users.filter(user => user.id !== this.authUserId);
            },

            get filteredEventsList() {
                let filtered = this.events.filter(event => {
                    const searchLower = this.searchQuery.toLowerCase();
                    return event.title.toLowerCase().includes(searchLower) ||
                           (event.description || '').toLowerCase().includes(searchLower);
                });

                // Sort events
                filtered.sort((a, b) => {
                    switch (this.sortBy) {
                        case 'date_asc':
                            return new Date(a.start_at) - new Date(b.start_at);
                        case 'date_desc':
                            return new Date(b.start_at) - new Date(a.start_at);
                        case 'title':
                            return a.title.localeCompare(b.title);
                        default:
                            return new Date(b.start_at) - new Date(a.start_at);
                    }
                });

                return filtered;
            },

            formatDate(datetime) {
                if (!datetime) return '';
                return new Date(datetime).toLocaleDateString('en-US', {
                    weekday: 'short',
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric'
                });
            },

            formatTime(datetime) {
                if (!datetime) return '';
                return new Date(datetime).toLocaleTimeString('en-US', {
                    hour: '2-digit',
                    minute: '2-digit'
                });
            },

            // View Modal Functions
            openViewModal(event) {
                this.viewEventData = event;
                this.showViewModal = true;
            },

            closeViewModal() {
                this.showViewModal = false;
                this.viewEventData = null;
            },

            // Edit Modal Functions
            openEditModal(event) {
                this.editEventId = event.id;

                const startDate = new Date(event.start_at);
                const endDate = event.end_at ? new Date(event.end_at) : null;

                const formatLocalDate = (date) => {
                    const year = date.getFullYear();
                    const month = String(date.getMonth() + 1).padStart(2, '0');
                    const day = String(date.getDate()).padStart(2, '0');
                    return `${year}-${month}-${day}`;
                };

                const formatLocalTime = (date) => {
                    const hours = String(date.getHours()).padStart(2, '0');
                    const minutes = String(date.getMinutes()).padStart(2, '0');
                    return `${hours}:${minutes}`;
                };

                this.editEventData = {
                    title: event.title,
                    description: event.description || '',
                    date: formatLocalDate(startDate),
                    start_time: formatLocalTime(startDate),
                    end_time: endDate ? formatLocalTime(endDate) : '',
                    staff: event.staff ? event.staff.map(s => s.id) : []
                };

                this.editErrors = {};
                this.editGeneralError = '';
                this.showEditModal = true;
            },

            closeEditModal() {
                this.showEditModal = false;
                this.editEventId = null;
                this.editErrors = {};
                this.editGeneralError = '';
                this.isSubmitting = false;
                this.showEditStaffDropdown = false;
            },

            toggleEditStaff(userId) {
                const index = this.editEventData.staff.indexOf(userId);
                if (index === -1) {
                    this.editEventData.staff.push(userId);
                } else {
                    this.editEventData.staff.splice(index, 1);
                }
            },

            isEditStaffSelected(userId) {
                return this.editEventData.staff.includes(userId);
            },

            getEditSelectedStaffNames() {
                return this.users
                    .filter(user => this.editEventData.staff.includes(user.id))
                    .map(user => user.name)
                    .join(', ');
            },

            async updateEvent() {
                if (this.isSubmitting) return;

                this.isSubmitting = true;
                this.editErrors = {};
                this.editGeneralError = '';

                const startLocal = new Date(`${this.editEventData.date}T${this.editEventData.start_time}:00`);
                const startDateTime = startLocal.toISOString();

                let endDateTime = null;
                if (this.editEventData.end_time) {
                    const endLocal = new Date(`${this.editEventData.date}T${this.editEventData.end_time}:00`);
                    if (this.editEventData.end_time < this.editEventData.start_time) {
                        endLocal.setDate(endLocal.getDate() + 1);
                    }
                    endDateTime = endLocal.toISOString();
                }

                try {
                    const response = await fetch(`/events/${this.editEventId}`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            title: this.editEventData.title,
                            description: this.editEventData.description,
                            start_at: startDateTime,
                            end_at: endDateTime,
                            staff: this.editEventData.staff
                        })
                    });

                    const data = await response.json();

                    if (response.ok) {
                        // Update the event in the local list
                        const index = this.events.findIndex(e => e.id === this.editEventId);
                        if (index !== -1) {
                            this.events[index] = data.event;
                        }
                        this.closeEditModal();
                        window.showSuccess(data.message || 'Event updated successfully!');
                    } else {
                        if (data.errors) {
                            this.editErrors = data.errors;
                        } else {
                            this.editGeneralError = data.message || 'An error occurred while updating the event.';
                        }
                    }
                } catch (error) {
                    console.error('Error updating event:', error);
                    this.editGeneralError = 'Network error. Please try again.';
                } finally {
                    this.isSubmitting = false;
                }
            },

            // Delete Modal Functions
            openDeleteModal() {
                this.deleteEventId = this.editEventId;
                this.showDeleteModal = true;
            },

            closeDeleteModal() {
                this.showDeleteModal = false;
                this.deleteEventId = null;
            },

            async deleteEvent() {
                if (!this.deleteEventId || this.isSubmitting) return;

                this.isSubmitting = true;

                try {
                    const response = await fetch(`/events/${this.deleteEventId}`, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    });

                    if (response.ok) {
                        this.events = this.events.filter(e => e.id !== this.deleteEventId);
                        this.closeDeleteModal();
                        this.closeEditModal();
                        window.showSuccess('Event cancelled successfully!');
                    } else {
                        const data = await response.json();
                        this.editGeneralError = data.message || 'Failed to cancel event.';
                    }
                } catch (error) {
                    console.error('Error cancelling event:', error);
                    this.editGeneralError = 'An error occurred while cancelling the event.';
                } finally {
                    this.isSubmitting = false;
                }
            }
        }
    }
    </script>
</x-app-layout>
