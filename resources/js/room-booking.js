document.addEventListener("alpine:init", () => {
    // Original modal-based room booking flow
    Alpine.data("roomBookingFlow", () => ({
        showRoomBookingFlow: false,
        currentStep: 1,
        selectedRoomId: null,
        selectedDate: "",
        selectedStartTime: "",
        selectedEndTime: "",
        previousBookings: [],
        clashDetected: false,
        clashMessage: "",
        numberOfStudents: null,
        equipmentNeeded: "",
        purpose: "",

        init() {
            this.$watch("selectedRoomId", async (newRoomId) => {
                if (newRoomId) {
                    await this.fetchPreviousBookings();
                    this.initCalendar();
                } else {
                    this.previousBookings = [];
                }
            });

            this.$root.addEventListener("close-room-booking", () => {
                this.showRoomBookingFlow = false;
            });
        },

        initCalendar() {
            // console.log("Initializing calendar with bookings:", [...this.previousBookings][0].purpose);
            const events = this.previousBookings.map((b) => ({
                id: b.id,
                title: b.purpose + (b.room_id ? ` (Room ${b.room_id})` : ""),
                start: b.start_time,
                end: b.end_time,
                backgroundColor:
                    b.status === true
                        ? "#22c55e"
                        : b.status === false
                        ? "#ef4444"
                        : "#facc15",
                borderColor: "#888",
                extendedProps: {
                    number_of_student: b.number_of_student,
                    equipment_needed: b.equipment_needed,
                    user_id: b.user_id,
                },
            }));

            const calendarEl = document.getElementById("calendar");
            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: "dayGridMonth",
                height: "auto",
                headerToolbar: {
                    left: "prev,next today",
                    center: "title",
                    right: "dayGridMonth,timeGridWeek,timeGridDay",
                },
                events: events,
                // eventOverlap: false,
                // slotEventOverlap: false,
                eventClick: function (info) {
                    const props = info.event.extendedProps;
                    let details = `目的: ${info.event.title}\n`;
                    details += `開始: ${info.event.start.toLocaleString()}\n終了: ${
                        info.event.end ? info.event.end.toLocaleString() : ""
                    }\n`;
                    details += `人数: ${props.number_of_student}\n`;
                    if (props.equipment_needed)
                        details += `備品: ${props.equipment_needed}\n`;
                    alert(details);
                },
            });
            calendar.render();
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

        async fetchPreviousBookings() {
            if (!this.selectedRoomId) {
                this.previousBookings = [];
                return;
            }
            try {
                const response = await fetch(
                    `/bookings/room/${this.selectedRoomId}`
                );
                if (!response.ok) {
                    throw new Error("Failed to fetch previous bookings");
                }
                this.previousBookings = await response.json();
                // console.log("Previous Bookings:", this.previousBookings);
            } catch (error) {
                console.error("Error fetching previous bookings:", error);
                this.previousBookings = [];
            }
        },

        async checkClash() {
            if (
                !this.selectedDate ||
                !this.selectedStartTime ||
                !this.selectedEndTime
            ) {
                alert("日付と時間を選択してください。");
                return;
            }

            const newBookingStart = new Date(
                `${this.selectedDate}T${this.selectedStartTime}:00`
            );
            const newBookingEnd = new Date(
                `${this.selectedDate}T${this.selectedEndTime}:00`
            );

            const clashes = this.previousBookings.filter((booking) => {
                const existingStart = new Date(booking.start_time);
                const existingEnd = new Date(booking.end_time);

                return (
                    newBookingStart < existingEnd &&
                    newBookingEnd > existingStart
                );
            });

            if (clashes.length > 0) {
                this.clashDetected = true;
                this.clashMessage =
                    "選択された時間は既に予約済みです。別の時間を選択してください。";
            } else {
                this.clashDetected = false;
                this.clashMessage = "";
                this.currentStep = 3;
            }
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
