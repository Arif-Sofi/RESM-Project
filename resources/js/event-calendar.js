// Event Calendar Alpine.js Component
export default function (users, authUserId, initialEvents = []) {
    return {
        currentView: 'calendar',
        users: users,
        events: initialEvents,
        calendar: null,
        authUserId: authUserId,

        // List view state
        searchQuery: '',
        sortBy: 'date_desc',

        // View modal state
        showViewModal: false,
        viewEventData: null,

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

        // Create modal state
        showCreateModal: false,
        createEventData: {
            title: '',
            description: '',
            location: '',
            date: '',
            start_time: '',
            end_time: '',
            staff: []
        },
        createErrors: {},
        createGeneralError: '',

        // Delete modal state
        showDeleteModal: false,
        deleteEventId: null,

        // Staff dropdown state
        showStaffDropdown: false,
        showEditStaffDropdown: false,

        // Filter out current user from staff list (they're already the creator)
        get availableStaff() {
            return this.users.filter(user => user.id !== this.authUserId);
        },

        // Filtered and sorted events for list view
        get filteredEventsList() {
            let filtered = this.events.filter(event => {
                const searchLower = this.searchQuery.toLowerCase();
                return event.title.toLowerCase().includes(searchLower) ||
                       (event.description || '').toLowerCase().includes(searchLower) ||
                       (event.creator?.name || '').toLowerCase().includes(searchLower);
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

        init() {
            this.initCalendar();
            this.loadCalendarEvents();
        },

        initCalendar() {
            const calendarEl = document.getElementById('event-calendar');
            this.calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                slotMinTime: '07:00:00',
                slotMaxTime: '22:00:00',
                allDaySlot: false,
                height: 'auto',
                selectable: true,
                selectMirror: true,
                select: this.handleDateSelect.bind(this),
                eventClick: this.handleEventClick.bind(this),
                events: [],
                eventDisplay: 'block',
                displayEventTime: true,
                displayEventEnd: true,
            });
            this.calendar.render();
        },

        loadCalendarEvents() {
            fetch('/api/events')
                .then(response => response.json())
                .then(data => {
                    this.events = data;
                    this.updateCalendarEvents();
                })
                .catch(error => console.error('Error loading events:', error));
        },

        updateCalendarEvents() {
            const calendarEvents = this.events.map(event => {
                // Determine color based on status and whether user is creator
                let color;
                if (event.status === 'COMPLETED') {
                    color = '#6b7280'; // Gray for completed events
                } else {
                    color = event.user_id === this.authUserId ? '#3b82f6' : '#10b981';
                }

                return {
                    id: event.id,
                    title: event.title,
                    start: event.start_at,
                    end: event.end_at,
                    backgroundColor: color,
                    borderColor: color,
                    extendedProps: {
                        event: event
                    }
                };
            });

            this.calendar.removeAllEvents();
            this.calendar.addEventSource(calendarEvents);
        },

        handleDateSelect(selectInfo) {
            const date = selectInfo.startStr.split('T')[0];
            const startTime = selectInfo.startStr.includes('T')
                ? selectInfo.startStr.split('T')[1].substring(0, 5)
                : '09:00';
            const endTime = selectInfo.endStr.includes('T')
                ? selectInfo.endStr.split('T')[1].substring(0, 5)
                : '10:00';

            this.openCreateModal(date, startTime, endTime);
            this.calendar.unselect();
        },

        handleEventClick(clickInfo) {
            const event = clickInfo.event.extendedProps.event;

            // If user is the creator, open edit modal
            if (event.user_id === this.authUserId) {
                this.openEditModal(event);
            } else {
                // Open view modal for read-only details
                this.openViewModal(event);
            }
        },

        // View Modal Methods
        openViewModal(event) {
            this.viewEventData = event;
            this.showViewModal = true;
        },

        closeViewModal() {
            this.showViewModal = false;
            this.viewEventData = null;
        },

        // Create Modal Methods
        openCreateModal(date = null, startTime = null, endTime = null) {
            this.createEventData = {
                title: '',
                description: '',
                location: '',
                date: date || new Date().toISOString().split('T')[0],
                start_time: startTime || '09:00',
                end_time: endTime || '10:00',
                staff: []
            };

            this.createErrors = {};
            this.createGeneralError = '';
            this.showCreateModal = true;
        },

        closeCreateModal() {
            this.showCreateModal = false;
            this.createErrors = {};
            this.createGeneralError = '';
            this.isSubmitting = false;
            this.showStaffDropdown = false;
        },

        toggleStaff(userId) {
            const index = this.createEventData.staff.indexOf(userId);
            if (index === -1) {
                this.createEventData.staff.push(userId);
            } else {
                this.createEventData.staff.splice(index, 1);
            }
        },

        isStaffSelected(userId) {
            return this.createEventData.staff.includes(userId);
        },

        toggleAllCreateStaff() {
             const allStaffIds = this.availableStaff.map(user => user.id);
             if (this.isAllCreateStaffSelected()) {
                 this.createEventData.staff = [];
             } else {
                 this.createEventData.staff = [...allStaffIds];
             }
        },

        isAllCreateStaffSelected() {
             return this.availableStaff.length > 0 && this.createEventData.staff.length === this.availableStaff.length;
        },

        getSelectedStaffNames() {
            return this.users
                .filter(user => this.createEventData.staff.includes(user.id))
                .map(user => user.name)
                .join(', ');
        },

        async createEvent() {
            if (this.isSubmitting) return;

            this.isSubmitting = true;
            this.createErrors = {};
            this.createGeneralError = '';

            // Convert local time to ISO string (UTC) for server storage
            const startLocal = new Date(`${this.createEventData.date}T${this.createEventData.start_time}:00`);
            const startDateTime = startLocal.toISOString();

            let endDateTime = null;
            if (this.createEventData.end_time) {
                const endLocal = new Date(`${this.createEventData.date}T${this.createEventData.end_time}:00`);
                // Handle events crossing midnight
                if (this.createEventData.end_time < this.createEventData.start_time) {
                    endLocal.setDate(endLocal.getDate() + 1);
                }
                endDateTime = endLocal.toISOString();
            }

            try {
                const response = await fetch('/events', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        title: this.createEventData.title,
                        description: this.createEventData.description,
                        location: this.createEventData.location,
                        start_at: startDateTime,
                        end_at: endDateTime,
                        staff: this.createEventData.staff
                    })
                });

                const data = await response.json();

                if (response.ok) {
                    this.closeCreateModal();
                    this.loadCalendarEvents();
                    window.showSuccess(data.message || 'Event created successfully!');
                } else {
                    if (data.errors) {
                        this.createErrors = data.errors;
                    } else {
                        this.createGeneralError = data.message || 'An error occurred while creating the event.';
                    }
                }
            } catch (error) {
                console.error('Error creating event:', error);
                this.createGeneralError = 'Network error. Please try again.';
            } finally {
                this.isSubmitting = false;
            }
        },

        // Edit Modal Methods
        openEditModal(event) {
            this.editEventId = event.id;

            // Parse UTC timestamps and convert to local time for display
            const startDate = new Date(event.start_at);
            const endDate = event.end_at ? new Date(event.end_at) : null;

            // Format date as YYYY-MM-DD in local timezone
            const formatLocalDate = (date) => {
                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const day = String(date.getDate()).padStart(2, '0');
                return `${year}-${month}-${day}`;
            };

            // Format time as HH:mm in local timezone
            const formatLocalTime = (date) => {
                const hours = String(date.getHours()).padStart(2, '0');
                const minutes = String(date.getMinutes()).padStart(2, '0');
                return `${hours}:${minutes}`;
            };

            this.editEventData = {
                title: event.title,
                description: event.description || '',
                location: event.location || '',
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

        toggleAllEditStaff() {
             const allStaffIds = this.availableStaff.map(user => user.id);
             if (this.isAllEditStaffSelected()) {
                 this.editEventData.staff = [];
             } else {
                 this.editEventData.staff = [...allStaffIds];
             }
        },

        isAllEditStaffSelected() {
             return this.availableStaff.length > 0 && this.editEventData.staff.length === this.availableStaff.length;
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

            // Convert local time to ISO string (UTC) for server storage
            const startLocal = new Date(`${this.editEventData.date}T${this.editEventData.start_time}:00`);
            const startDateTime = startLocal.toISOString();

            let endDateTime = null;
            if (this.editEventData.end_time) {
                const endLocal = new Date(`${this.editEventData.date}T${this.editEventData.end_time}:00`);
                // Handle events crossing midnight
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
                        location: this.editEventData.location,
                        start_at: startDateTime,
                        end_at: endDateTime,
                        staff: this.editEventData.staff
                    })
                });

                const data = await response.json();

                if (response.ok) {
                    this.closeEditModal();
                    this.loadCalendarEvents();
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

        // Delete Modal Methods
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
                    this.closeDeleteModal();
                    this.closeEditModal();
                    this.loadCalendarEvents();
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
        },

        // Utility Methods
        formatDate(datetime) {
            if (!datetime) return '';
            return new Date(datetime).toLocaleDateString('en-US', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
        },

        formatTime(datetime) {
            if (!datetime) return '';
            return new Date(datetime).toLocaleTimeString('en-US', {
                hour: '2-digit',
                minute: '2-digit'
            });
        }
    }
}
