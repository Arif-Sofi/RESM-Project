document.addEventListener('alpine:init', () => {
    Alpine.data('bookingModal', () => ({
        currentStep: 1,
        selectedRoomId: null,
        selectedDate: '',
        selectedTime: '',
        previousBookings: [],
        numberOfStudents: '',
        equipmentNeeded: '',
        purpose: '',
        clashDetected: false,
        clashMessage: '',
        fetchBookings() {
            if (this.selectedRoomId) {
                fetch(`/bookings/room/${this.selectedRoomId}`)
                    .then(response => response.json())
                    .then(data => {
                        this.previousBookings = data;
                    })
                    .catch(error => {
                        console.error('Error fetching bookings:', error);
                        this.previousBookings = [];
                    });
            }
        },
        checkClash() {
            this.clashDetected = false;
            this.clashMessage = '';
            if (!this.selectedDate || !this.selectedTime) {
                this.clashDetected = true;
                this.clashMessage = 'Please select both date and time.';
                return;
            }

            fetch('/bookings/check-clash', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')},
                body: JSON.stringify({
                    room_id: this.selectedRoomId,
                    date: this.selectedDate,
                    time: this.selectedTime
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.clash) {
                    this.clashDetected = true;
                    this.clashMessage = data.message || 'The selected time clashes with an existing booking.';
                } else {
                    this.currentStep = 2;
                }
            })
            .catch(error => {
                console.error('Error checking clash:', error);
                this.clashDetected = true;
                this.clashMessage = 'An error occurred while checking for clashes.';
            });
        },
        init() {
            this.$watch('selectedRoomId', () => this.fetchBookings());
            this.$root.addEventListener('open-booking-details-modal', (event) => {
                console.log('bookingModal component received open-booking-details-modal event!');
                console.log('Event details:', event.detail);

                this.selectedRoomId = event.detail.roomId;
                this.currentStep = 1;
                this.$nextTick(() => this.fetchBookings());
                this.$dispatch('open-modal', 'booking-details-modal');
            });
        }
    }));
});
