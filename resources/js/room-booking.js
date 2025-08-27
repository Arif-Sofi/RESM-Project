document.addEventListener('alpine:init', () => {
    // Original modal-based room booking flow
    Alpine.data('roomBookingFlow', () => ({
        showRoomBookingFlow: false,
        currentStep: 1,
        selectedRoomId: null,
        selectedDate: '',
        selectedStartTime: '',
        selectedEndTime: '',
        previousBookings: [],
        clashDetected: false,
        clashMessage: '',
        numberOfStudents: null,
        equipmentNeeded: '',
        purpose: '',

        init() {
            this.$watch('selectedRoomId', (newRoomId) => {
                if (newRoomId) {
                    this.fetchPreviousBookings();
                } else {
                    this.previousBookings = [];
                }
            });

            this.$root.addEventListener('close-room-booking', () => {
                this.showRoomBookingFlow = false;
            });
        },

        showRoomBooking() {
            this.showRoomBookingFlow = !this.showRoomBookingFlow;
            if (this.showRoomBookingFlow) {
                this.$dispatch('close-date-booking');
                this.resetForm();
            }
        },

        hideRoomBooking() {
            this.showRoomBookingFlow = false;
            this.resetForm();
        },

        async fetchPreviousBookings() {
            if (!this.selectedRoomId) {
                this.previousBookings = [];
                return;
            }
            try {
                const response = await fetch(`/bookings/room/${this.selectedRoomId}`);
                if (!response.ok) {
                    throw new Error('Failed to fetch previous bookings');
                }
                this.previousBookings = await response.json();
            } catch (error) {
                console.error('Error fetching previous bookings:', error);
                this.previousBookings = [];
            }
        },

        async checkClash() {
            if (!this.selectedDate || !this.selectedStartTime || !this.selectedEndTime) {
                alert('日付と時間を選択してください。');
                return;
            }

            const newBookingStart = new Date(`${this.selectedDate}T${this.selectedStartTime}:00`);
            const newBookingEnd = new Date(`${this.selectedDate}T${this.selectedEndTime}:00`);

            const clashes = this.previousBookings.filter(booking => {
                const existingStart = new Date(booking.start_time);
                const existingEnd = new Date(booking.end_time);

                return (newBookingStart < existingEnd && newBookingEnd > existingStart);
            });

            if (clashes.length > 0) {
                this.clashDetected = true;
                this.clashMessage = '選択された時間は既に予約済みです。別の時間を選択してください。';
            } else {
                this.clashDetected = false;
                this.clashMessage = '';
                this.currentStep = 3;
            }
        },

        resetForm() {
            this.currentStep = 1;
            this.selectedRoomId = null;
            this.selectedDate = '';
            this.selectedStartTime = '';
            this.selectedEndTime = '';
            this.previousBookings = [];
            this.clashDetected = false;
            this.clashMessage = '';
            this.numberOfStudents = null;
            this.equipmentNeeded = '';
            this.purpose = '';
        }
    }));
});
