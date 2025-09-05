document.addEventListener("alpine:init", () => {
    // Original modal-based room booking flow
    Alpine.data("roomBookingFlow", () => ({
        showRoomBookingFlow: false,
        currentStep: 1,
        selectedRoomId: null,
        selectedDate: "",
        selectedStartTime: "",
        selectedEndTime: "",
        clashDetected: false,
        clashMessage: "",
        numberOfStudents: null,
        equipmentNeeded: "",
        purpose: "",

        init() {
            this.$root.addEventListener("close-room-booking", () => {
                this.showRoomBookingFlow = false;
            });
        },

        showRoomBooking() {
            this.showRoomBookingFlow = !this.showRoomBookingFlow;
            if (this.showRoomBookingFlow) {
                this.$dispatch("close-date-booking");
                this.resetForm();
            }
        },

        hideRoomBooking() {
            this.showRoomBookingFlow = false;
            this.resetForm();
        },


        resetForm() {
            this.currentStep = 1;
            this.selectedRoomId = null;
            this.selectedDate = "";
            this.selectedStartTime = "";
            this.selectedEndTime = "";
            this.previousBookings = [];
            this.clashDetected = false;
            this.clashMessage = "";
            this.numberOfStudents = null;
            this.equipmentNeeded = "";
            this.purpose = "";
        },
    }));
});
