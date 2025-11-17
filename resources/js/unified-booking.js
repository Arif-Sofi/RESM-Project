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

        init() {
            this.initCalendar();
            this.loadCalendarEvents();
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
            this.showBookingForm = true;
            this.scrollToForm();
        },

        selectRoomForBooking(room) {
            this.selectedRoom = room;
            this.showBookingForm = true;
            this.checkAvailability();
            this.scrollToForm();
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
            this.selectedDate = selectInfo.startStr.split('T')[0];
            this.selectedStartTime = selectInfo.startStr;
            this.selectedEndTime = selectInfo.endStr;
            this.showBookingForm = true;

            // If no room selected, show available rooms
            if (!this.selectedRoom) {
                this.checkAvailableRooms();
            } else {
                this.checkAvailability();
            }

            this.calendar.unselect();
            this.scrollToForm();
        },

        handleEventClick(clickInfo) {
            const booking = clickInfo.event.extendedProps.booking;
            const roomName = booking.room?.name || this.selectedRoom?.name || 'Room';
            const statusText = booking.status === true ? 'Approved' : booking.status === false ? 'Rejected' : 'Pending';

            // Navigate to booking details or open modal
            alert(`Booking Details:\nRoom: ${roomName}\nTime: ${new Date(booking.start_time).toLocaleString()} - ${new Date(booking.end_time).toLocaleString()}\nStatus: ${statusText}\nPurpose: ${booking.purpose || 'N/A'}`);
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
                const startTime = document.getElementById('start_time')?.value || '09:00';
                const endTime = document.getElementById('end_time')?.value || '10:00';
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
        }
    }
}
