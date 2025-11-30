// Event Calendar Alpine.js Component
export default function (users, authUserId) {
    return {
        currentView: 'calendar',
        users: users,
        events: [],
        calendar: null,
        authUserId: authUserId,

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
                // Determine color based on whether user is creator
                let color = event.user_id === this.authUserId ? '#3b82f6' : '#10b981';

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

            const startDateTime = `${this.createEventData.date}T${this.createEventData.start_time}:00`;
            const endDateTime = this.createEventData.end_time
                ? `${this.createEventData.date}T${this.createEventData.end_time}:00`
                : null;

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

            const startDate = new Date(event.start_at);
            const endDate = event.end_at ? new Date(event.end_at) : null;

            this.editEventData = {
                title: event.title,
                description: event.description || '',
                date: startDate.toISOString().split('T')[0],
                start_time: startDate.toTimeString().substring(0, 5),
                end_time: endDate ? endDate.toTimeString().substring(0, 5) : '',
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

            const startDateTime = `${this.editEventData.date}T${this.editEventData.start_time}:00`;
            const endDateTime = this.editEventData.end_time
                ? `${this.editEventData.date}T${this.editEventData.end_time}:00`
                : null;

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
                    window.showSuccess('Event deleted successfully!');
                } else {
                    const data = await response.json();
                    this.editGeneralError = data.message || 'Failed to delete event.';
                }
            } catch (error) {
                console.error('Error deleting event:', error);
                this.editGeneralError = 'An error occurred while deleting the event.';
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
