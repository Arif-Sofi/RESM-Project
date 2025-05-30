<x-modal name="booking-details-modal" focusable
    x-data="{
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
                    'X-CSRF-TOKEN': document.head.querySelector('meta[name='csrf-token']').content
                },
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
        }
    }"
    x-on:open-booking-details-modal.window="
        selectedRoomId = $event.detail.roomId;
        currentStep = 1;
        $nextTick(() => fetchBookings());
        $dispatch('open-modal', 'booking-details-modal');
    "
>
    <form method="POST" action="{{ route('bookings.store') }}" class="p-6">
        @csrf
        <input type="hidden" name="room_id" x-model="selectedRoomId">
        <input type="hidden" name="start_time" :value="selectedDate + ' ' + selectedTime + ':00'">
        <input type="hidden" name="end_time" :value="selectedDate + ' ' + selectedTime + ':00'"> {{-- Assuming 1-hour booking for now, adjust as needed --}}

        {{-- Step 1: Display Previous Bookings & Date/Time Selection --}}
        <div x-show="currentStep === 1">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                {{ __('Select Date and Time') }}
            </h2>

            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                {{ __('Previous bookings for this room:') }}
            </p>

            <div class="mt-4 max-h-40 overflow-y-auto border p-2 rounded">
                <template x-if="previousBookings.length > 0">
                    <ul>
                        <template x-for="booking in previousBookings" :key="booking.id">
                            <li class="text-sm text-gray-700 dark:text-gray-300">
                                <span x-text="new Date(booking.start_time).toLocaleString()"></span> - <span x-text="new Date(booking.end_time).toLocaleString()"></span>
                                (<span x-text="booking.purpose"></span>)
                            </li>
                        </template>
                    </ul>
                </template>
                <template x-if="previousBookings.length === 0">
                    <p class="text-sm text-gray-500">{{ __('No previous bookings found for this room.') }}</p>
                </template>
            </div>

            <div class="mt-6">
                <div class="mb-4">
                    <x-input-label for="booking_date" :value="__('Date')" />
                    <x-text-input id="booking_date" name="booking_date_step1" type="date" class="mt-1 block w-full" x-model="selectedDate" />
                </div>
                <div class="mb-4">
                    <x-input-label for="booking_time" :value="__('Time')" />
                    <x-text-input id="booking_time" name="booking_time_step1" type="time" class="mt-1 block w-full" x-model="selectedTime" />
                </div>
                <p x-show="clashDetected" class="text-sm text-red-600 dark:text-red-400 mt-2" x-text="clashMessage"></p>
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('messages.cancel') }}
                </x-secondary-button>
                <x-primary-button class="ms-3" x-on:click="checkClash()">
                    {{ __('messages.next') }}
                </x-primary-button>
            </div>
        </div>

        {{-- Step 2: Input Booking Details --}}
        <div x-show="currentStep === 2">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                {{ __('Booking Details') }}
            </h2>

            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                {{ __('Please provide additional details for your booking.') }}
            </p>

            <div class="mt-6">
                <div class="mb-4">
                    <x-input-label for="number_of_students" :value="__('Number of Students')" />
                    <x-text-input id="number_of_students" name="number_of_student" type="number" class="mt-1 block w-full" x-model="numberOfStudents" required />
                </div>
                <div class="mb-4">
                    <x-input-label for="equipment_needed" :value="__('Equipment Needed (Optional)')" />
                    <textarea id="equipment_needed" name="equipment_needed" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" x-model="equipmentNeeded"></textarea>
                </div>
                <div class="mb-4">
                    <x-input-label for="purpose" :value="__('Purpose')" />
                    <textarea id="purpose" name="purpose" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" x-model="purpose" required></textarea>
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="currentStep = 1">
                    {{ __('messages.back') }}
                </x-secondary-button>
                <x-primary-button class="ms-3" type="submit">
                        {{ __('messages.save') }}
                </x-primary-button>
            </div>
        </div>
    </form>
</x-modal>
