// Unified Booking Alpine.js Component
export default function (rooms, authUserId) {
    return {
        currentView: 'calendar',
        rooms: rooms,
        bookings: [],
        selectedRoom: null,
        selectedDate: null,
        selectedStartTime: null,
        selectedEndTime: null,
        roomSearch: '',
        showBookingForm: false,
        availableRooms: [],
        calendar: null,
        clashError: '',
        authUserId: authUserId,

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

        // Create modal state
        showCreateModal: false,
        createBookingData: {
            room_id: '',
            date: '',
            start_time: '',
            end_time: '',
            number_of_student: '',
            equipment_needed: '',
            purpose: ''
        },
        createClashError: '',
        createErrors: {},
        createGeneralError: '',

        // List view state (merged from bookingsList)
        searchQuery: '',
        statusFilter: 'all',
        sortBy: 'date_desc',
        expandedBooking: null,

        init() {
            this.initCalendar();
            this.loadCalendarEvents();
            this.checkUrlHash();
        },

        checkUrlHash() {
            // Check if URL has a booking hash (e.g., #booking-32)
            const hash = window.location.hash;
            if (hash && hash.startsWith('#booking-')) {
                // Check if we're navigating from the same site (internal navigation)
                const isInternalNavigation = document.referrer &&
                    new URL(document.referrer).origin === window.location.origin;

                // If internal navigation, just clear the hash without opening modal
                if (isInternalNavigation) {
                    window.history.replaceState(null, '', window.location.pathname);
                    return;
                }

                // Otherwise, it's a deep link - open the modal
                const bookingId = parseInt(hash.replace('#booking-', ''));
                if (bookingId) {
                    this.openEditModalFromHash(bookingId);
                }
            }
        },

        async openEditModalFromHash(bookingId) {
            // Clear the hash immediately to prevent flash on navigation
            window.history.replaceState(null, '', window.location.pathname);

            try {
                const response = await fetch(`/api/bookings/${bookingId}`);
                if (response.ok) {
                    const booking = await response.json();
                    // Check if user owns this booking and it's pending
                    if (booking.user_id === this.authUserId && booking.status === null) {
                        this.openEditModal(booking);
                    }
                }
            } catch (error) {
                console.error('Error loading booking:', error);
            }
        },

        toggleView(view) {
            this.currentView = view;
        },

        get filteredRooms() {
            if (!this.roomSearch) return this.rooms;
            const search = this.roomSearch.toLowerCase();
            return this.rooms.filter(room =>
                room.name.toLowerCase().includes(search) ||
                (room.location_details && room.location_details.toLowerCase().includes(search))
            );
        },

        selectRoom(room) {
            this.selectedRoom = room;
            this.loadCalendarEvents();

            // Open create modal instead of inline form
            const today = new Date().toISOString().split('T')[0];
            this.openCreateModal(room, today, null, null);
        },

        selectRoomForBooking(room) {
            // Open modal with pre-selected room and time
            this.openCreateModal(
                room,
                this.selectedDate,
                this.selectedStartTime,
                this.selectedEndTime
            );
        },

        initCalendar() {
            const calendarEl = document.getElementById('calendar');
            this.calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'timeGridWeek',
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
                allDaySlot: false,
            });
            this.calendar.render();
        },

        loadCalendarEvents() {
            let url = '/api/bookings';
            if (this.selectedRoom) {
                url = `/bookings/room/${this.selectedRoom.id}`;
            }

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    this.bookings = data;
                    this.updateCalendarEvents();
                })
                .catch(error => console.error('Error loading bookings:', error));
        },

        updateCalendarEvents() {
            const events = this.bookings.map(booking => {
                let color = '#fbbf24'; // yellow for pending
                if (booking.status === true) color = '#10b981'; // green for approved
                if (booking.status === false) color = '#ef4444'; // red for rejected

                let borderColor = color;
                let borderWidth = 1;

                // Highlight user's own bookings with thicker blue border
                if (booking.user_id === this.authUserId) {
                    borderColor = '#3b82f6';
                    borderWidth = 3;
                }

                // Handle cases where room or user data might be missing
                const roomName = booking.room?.name || this.selectedRoom?.name || 'Room';
                const userName = booking.user?.name || '';
                const title = userName ? `${roomName} - ${userName}` : roomName;

                return {
                    id: booking.id,
                    title: title,
                    start: booking.start_time,
                    end: booking.end_time,
                    backgroundColor: color,
                    borderColor: borderColor,
                    borderWidth: borderWidth,
                    extendedProps: {
                        booking: booking
                    }
                };
            });

            this.calendar.removeAllEvents();
            this.calendar.addEventSource(events);
        },

        handleDateSelect(selectInfo) {
            const date = selectInfo.startStr.split('T')[0];
            const startTime = selectInfo.startStr;
            const endTime = selectInfo.endStr;

            // Store selected date/times for available rooms check
            this.selectedDate = date;
            this.selectedStartTime = startTime;
            this.selectedEndTime = endTime;

            // If no room selected, show available rooms section
            if (!this.selectedRoom) {
                this.checkAvailableRooms();
            } else {
                // Directly open modal with selected room and time
                this.openCreateModal(this.selectedRoom, date, startTime, endTime);
            }

            this.calendar.unselect();
        },

        handleEventClick(clickInfo) {
            const booking = clickInfo.event.extendedProps.booking;

            // If user owns the booking and it's pending, open edit modal
            if (booking.user_id === this.authUserId && booking.status === null) {
                this.openEditModal(booking);
            } else {
                // Show read-only details for other bookings
                const roomName = booking.room?.name || this.selectedRoom?.name || 'Room';
                const statusText = booking.status === true ? 'Approved' : booking.status === false ? 'Rejected' : 'Pending';
                alert(`Booking Details:\nRoom: ${roomName}\nTime: ${new Date(booking.start_time).toLocaleString()} - ${new Date(booking.end_time).toLocaleString()}\nStatus: ${statusText}\nPurpose: ${booking.purpose || 'N/A'}`);
            }
        },

        checkAvailableRooms() {
            if (!this.selectedStartTime || !this.selectedEndTime) return;

            fetch(`/api/bookings/available-rooms?start=${this.selectedStartTime}&end=${this.selectedEndTime}`)
                .then(response => response.json())
                .then(data => {
                    this.availableRooms = data.available_rooms || [];
                })
                .catch(error => console.error('Error checking availability:', error));
        },

        checkAvailability() {
            if (!this.selectedRoom || !this.selectedStartTime || !this.selectedEndTime) return;

            // Handle selectedRoom being either an object or an ID
            const roomId = typeof this.selectedRoom === 'object' ? this.selectedRoom.id : this.selectedRoom;

            fetch(`/api/bookings/check-availability`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    room_id: roomId,
                    start_time: this.selectedStartTime,
                    end_time: this.selectedEndTime
                })
            })
            .then(response => response.json())
            .then(data => {
                this.clashError = data.available ? '' : 'This time slot is already booked';
            })
            .catch(error => console.error('Error checking availability:', error));
        },

        scrollToForm() {
            this.$nextTick(() => {
                const formEl = this.$el.querySelector('[x-show="showBookingForm"]');
                if (formEl) {
                    formEl.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                }
            });
        },

        formatTimeForInput(datetime) {
            if (!datetime) return '';
            const date = new Date(datetime);
            return date.toTimeString().substring(0, 5); // HH:MM format
        },

        updateTimes() {
            // Update hidden datetime inputs when date changes
            if (this.selectedDate) {
                const startTime = document.getElementById('start_time_display')?.value || '09:00';
                const endTime = document.getElementById('end_time_display')?.value || '10:00';
                this.selectedStartTime = `${this.selectedDate}T${startTime}:00`;
                this.selectedEndTime = `${this.selectedDate}T${endTime}:00`;
            }
        },

        updateStartTime(event) {
            const time = event.target.value;
            if (this.selectedDate && time) {
                this.selectedStartTime = `${this.selectedDate}T${time}:00`;
                this.checkAvailability();
            }
        },

        updateEndTime(event) {
            const time = event.target.value;
            if (this.selectedDate && time) {
                this.selectedEndTime = `${this.selectedDate}T${time}:00`;
                this.checkAvailability();
            }
        },

        // Edit Modal Methods
        openEditModal(booking) {
            this.editBookingId = booking.id;

            // Parse start and end times
            const startDate = new Date(booking.start_time);
            const endDate = new Date(booking.end_time);

            // Populate form data
            this.editBookingData = {
                room_id: booking.room_id,
                date: startDate.toISOString().split('T')[0],
                start_time: startDate.toTimeString().substring(0, 5), // HH:MM
                end_time: endDate.toTimeString().substring(0, 5),     // HH:MM
                number_of_student: booking.number_of_student,
                equipment_needed: booking.equipment_needed || '',
                purpose: booking.purpose
            };

            // Clear errors
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
            this.isSubmitting = false;
        },

        updateEditTimes() {
            // Clear clash error when times change
            this.editClashError = '';
        },

        checkEditAvailability() {
            if (!this.editBookingData.room_id || !this.editBookingData.date ||
                !this.editBookingData.start_time || !this.editBookingData.end_time) {
                return;
            }

            const startDateTime = `${this.editBookingData.date}T${this.editBookingData.start_time}:00`;
            const endDateTime = `${this.editBookingData.date}T${this.editBookingData.end_time}:00`;

            fetch(`/api/bookings/check-availability`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    room_id: this.editBookingData.room_id,
                    start_time: startDateTime,
                    end_time: endDateTime,
                    booking_id: this.editBookingId
                })
            })
            .then(response => response.json())
            .then(data => {
                this.editClashError = data.available ? '' : 'This time slot is already booked';
            })
            .catch(error => console.error('Error checking availability:', error));
        },

        async updateBooking() {
            if (this.isSubmitting || this.editClashError) return;

            this.isSubmitting = true;
            this.editErrors = {};
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

                if (response.ok) {
                    // Success - reload calendar and close modal
                    this.closeEditModal();
                    this.loadCalendarEvents();

                    // Show success message (you can enhance this with a toast notification)
                    alert(data.message || 'Booking updated successfully!');
                } else {
                    // Validation errors
                    if (data.errors) {
                        this.editErrors = data.errors;
                    } else {
                        this.editGeneralError = data.message || 'An error occurred while updating the booking.';
                    }
                }
            } catch (error) {
                console.error('Error updating booking:', error);
                this.editGeneralError = 'Network error. Please try again.';
            } finally {
                this.isSubmitting = false;
            }
        },

        // Create Modal Methods
        openCreateModal(room = null, date = null, startTime = null, endTime = null) {
            // Pre-populate data if provided
            this.createBookingData = {
                room_id: room ? (typeof room === 'object' ? room.id : room) : '',
                date: date || new Date().toISOString().split('T')[0],
                start_time: startTime ? this.formatTimeForInput(startTime) : '09:00',
                end_time: endTime ? this.formatTimeForInput(endTime) : '10:00',
                number_of_student: '',
                equipment_needed: '',
                purpose: ''
            };

            // Clear errors
            this.createErrors = {};
            this.createClashError = '';
            this.createGeneralError = '';

            // Show modal
            this.showCreateModal = true;

            // Check availability if we have room and times
            if (this.createBookingData.room_id && this.createBookingData.date) {
                this.checkCreateAvailability();
            }
        },

        closeCreateModal() {
            this.showCreateModal = false;
            this.createErrors = {};
            this.createClashError = '';
            this.createGeneralError = '';
            this.isSubmitting = false;
        },

        updateCreateTimes() {
            // Clear clash error when times change
            this.createClashError = '';
        },

        checkCreateAvailability() {
            if (!this.createBookingData.room_id || !this.createBookingData.date ||
                !this.createBookingData.start_time || !this.createBookingData.end_time) {
                return;
            }

            const startDateTime = `${this.createBookingData.date}T${this.createBookingData.start_time}:00`;
            const endDateTime = `${this.createBookingData.date}T${this.createBookingData.end_time}:00`;

            fetch(`/api/bookings/check-availability`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    room_id: this.createBookingData.room_id,
                    start_time: startDateTime,
                    end_time: endDateTime
                })
            })
            .then(response => response.json())
            .then(data => {
                this.createClashError = data.available ? '' : 'This time slot is already booked';
            })
            .catch(error => console.error('Error checking availability:', error));
        },

        async createBooking() {
            if (this.isSubmitting || this.createClashError) return;

            this.isSubmitting = true;
            this.createErrors = {};
            this.createGeneralError = '';

            // Prepare datetime values in SQL format with UTC conversion
            const startDateTime = new Date(this.createBookingData.date + 'T' + this.createBookingData.start_time + ':00')
                .toISOString().slice(0, 19).replace('T', ' ');
            const endDateTime = new Date(this.createBookingData.date + 'T' + this.createBookingData.end_time + ':00')
                .toISOString().slice(0, 19).replace('T', ' ');

            try {
                const response = await fetch('/bookings', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        room_id: this.createBookingData.room_id,
                        start_time: startDateTime,
                        end_time: endDateTime,
                        number_of_student: this.createBookingData.number_of_student,
                        equipment_needed: this.createBookingData.equipment_needed,
                        purpose: this.createBookingData.purpose
                    })
                });

                const data = await response.json();

                if (response.ok) {
                    // Success - reload calendar and close modal
                    this.closeCreateModal();
                    this.loadCalendarEvents();

                    // Show success message (you can enhance this with a toast notification)
                    alert(data.message || 'Booking created successfully!');
                } else {
                    // Validation errors
                    if (data.errors) {
                        this.createErrors = data.errors;
                    } else {
                        this.createGeneralError = data.message || 'An error occurred while creating the booking.';
                    }
                }
            } catch (error) {
                console.error('Error creating booking:', error);
                this.createGeneralError = 'Network error. Please try again.';
            } finally {
                this.isSubmitting = false;
            }
        },

        // List View Methods (merged from bookingsList)
        get filteredBookings() {
            let filtered = this.bookings;

            // Filter by search
            if (this.searchQuery) {
                const query = this.searchQuery.toLowerCase();
                filtered = filtered.filter(booking =>
                    booking.room.name.toLowerCase().includes(query) ||
                    booking.purpose?.toLowerCase().includes(query) ||
                    booking.user?.name.toLowerCase().includes(query) ||
                    booking.user?.email.toLowerCase().includes(query)
                );
            }

            // Filter by status
            if (this.statusFilter !== 'all') {
                if (this.statusFilter === 'pending') {
                    filtered = filtered.filter(b => b.status === null);
                } else if (this.statusFilter === 'approved') {
                    filtered = filtered.filter(b => b.status === true);
                } else if (this.statusFilter === 'rejected') {
                    filtered = filtered.filter(b => b.status === false);
                }
            }

            // Sort
            if (this.sortBy === 'date_desc') {
                filtered.sort((a, b) => new Date(b.start_time) - new Date(a.start_time));
            } else if (this.sortBy === 'date_asc') {
                filtered.sort((a, b) => new Date(a.start_time) - new Date(b.start_time));
            } else if (this.sortBy === 'room') {
                filtered.sort((a, b) => a.room.name.localeCompare(b.room.name));
            } else if (this.sortBy === 'status') {
                filtered.sort((a, b) => {
                    const statusOrder = { null: 0, true: 1, false: 2 };
                    return statusOrder[a.status] - statusOrder[b.status];
                });
            }

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
        },

        showRejectModal(booking) {
            const reason = prompt('Please enter rejection reason:');
            if (reason) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/bookings/${booking.id}/reject`;
                form.innerHTML = `
                    <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').content}">
                    <input type="hidden" name="rejection_reason" value="${reason}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }
    }
}
