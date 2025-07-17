document.addEventListener('alpine:init', () => {
    Alpine.data('dateBookingFlow', () => ({
        currentStep: 1,
        selectedRoomId: null,
        selectedDate: '',
        selectedTime: '',
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

            this.$root.addEventListener('open-room-booking-flow-modal', (event) => {
                this.resetForm();
                this.$dispatch('open-modal', { name: 'room-booking-flow-modal' });
            });
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
                //this.previousBookings = jsonData.filter(item => item.start_time === "selectedDate" || item.end_time === "selectedDate");
                console.log('Selected date:', this.selectedDate); // debugging line
                console.log('Previous bookings fetched:', this.previousBookings.start_time); // debugging line
            } catch (error) {
                console.error('Error fetching previous bookings:', error);
                this.previousBookings = [];
            }
        },

        async checkClash() {
            if (!this.selectedDate || !this.selectedTime) {
                alert('日付と時間を選択してください。');
                return;
            }

            const newBookingStart = new Date(`${this.selectedDate}T${this.selectedTime}:00`);
            const newBookingEnd = new Date(newBookingStart.getTime() + 60 * 60 * 1000);

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
            this.selectedTime = '';
            this.previousBookings = [];
            this.clashDetected = false;
            this.clashMessage = '';
            this.numberOfStudents = null;
            this.equipmentNeeded = '';
            this.purpose = '';
        }
    }));
});
