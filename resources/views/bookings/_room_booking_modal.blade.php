<div x-show="showRoomBookingFlow" class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
    <!-- Step 1: Room Selection -->
    <div x-show="currentStep === 1">
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
            {{ __('messages.room_select') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400 mb-6">
            {{ __('messages.room_choose') }}
        </p>

        <div class="space-y-4">
            @foreach ($rooms as $room)
                <label
                    class="block cursor-pointer p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <div class="flex items-center">
                        <input type="radio" name="selected_room" value="{{ $room->id }}" x-model="selectedRoomId"
                            class="mr-4">
                        <div>
                            <span
                                class="font-semibold text-gray-900 dark:text-gray-100 text-lg">{{ $room->name }}</span>
                            <p class="text-gray-600 dark:text-gray-400 text-sm mt-1">
                                {{ $room->description }}</p>
                            <p class="text-gray-500 dark:text-gray-500 text-sm">
                                {{ $room->location_details }}</p>
                        </div>
                    </div>
                </label>
            @endforeach
        </div>

        <div class="mt-6 flex justify-end space-x-3">
            <x-secondary-button x-on:click="hideRoomBooking()">
                {{ __('messages.cancel') }}
            </x-secondary-button>
            <x-primary-button
                x-on:click="
                            if (selectedRoomId) {
                                currentStep = 2;
                            } else {
                                alert('{{ __('messages.room_choose') }}');
                            }
                        ">
                {{ __('messages.next') }}
            </x-primary-button>
        </div>
    </div>

    <!-- Step 2 & 3 Form -->
    <form method="POST" action="{{ route('bookings.store') }}" x-show="currentStep === 2 || currentStep === 3">
        @csrf
        <input type="hidden" name="room_id" x-model="selectedRoomId">
        <input type="hidden" name="start_time"
            :value="selectedDate && selectedStartTime ? new Date(selectedDate + 'T' + selectedStartTime + ':00')
                .toISOString().slice(0, 19).replace('T', ' ') : ''">
        <input type="hidden" name="end_time"
            :value="selectedDate && selectedEndTime ? new Date(selectedDate + 'T' + selectedEndTime + ':00')
                .toISOString().slice(0, 19).replace('T', ' ') : ''">

        <!-- Step 2: Date and Time Selection -->
        <div x-show="currentStep === 2">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                {{ __('Select Date and Time') }}
            </h2>

            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400 mb-4">
                {{ __('Previous bookings for this room:') }}
            </p>

            <!-- Previous Bookings Display -->
            <div
                class="mb-6 max-h-40 overflow-y-auto border border-gray-200 dark:border-gray-700 p-4 rounded-lg bg-gray-50 dark:bg-gray-900">
                <template x-if="previousBookings.length > 0">
                    <ul class="space-y-2">
                        <template x-for="booking in previousBookings" :key="booking.id">
                            <li
                                class="text-sm text-gray-700 dark:text-gray-300 p-2 bg-white dark:bg-gray-800 rounded border">
                                <div class="font-medium">
                                    <span x-text="new Date(booking.start_time).toLocaleString()"></span>
                                    -
                                    <span x-text="new Date(booking.end_time).toLocaleString()"></span>
                                </div>
                                <div class="text-gray-600 dark:text-gray-400">
                                    Purpose: <span x-text="booking.purpose"></span>
                                </div>
                                <div class="text-gray-500 dark:text-gray-500 text-xs">
                                    Booked by: {{ auth()->user()->name }}
                                </div>
                            </li>
                        </template>
                    </ul>
                </template>
                <template x-if="previousBookings.length === 0">
                    <p class="text-sm text-gray-500 text-center py-4">
                        {{ __('No previous bookings found for this room.') }}</p>
                </template>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div>
                    <x-input-label for="booking_date" :value="__('Date')" />
                    <x-text-input id="booking_date" name="booking_date" type="date" class="mt-1 block w-full"
                        x-model="selectedDate" />
                </div>
                <div>
                    <x-input-label for="start_time" :value="__('Start time')" />
                    <x-text-input id="start_time" name="start_time" type="time" class="mt-1 block w-full"
                        x-model="selectedStartTime" />
                </div>
                <div>
                    <x-input-label for="end_time" :value="__('End time')" />
                    <x-text-input id="end_time" name="end_time" type="time" class="mt-1 block w-full"
                        x-model="selectedEndTime" />
                </div>
            </div>

            <div x-show="clashDetected"
                class="mb-4 p-3 bg-red-100 dark:bg-red-900 border border-red-300 dark:border-red-700 rounded-lg">
                <p class="text-sm text-red-600 dark:text-red-400" x-text="clashMessage"></p>
            </div>

            <div class="flex justify-end space-x-3">
                <x-secondary-button x-on:click.prevent="currentStep = 1">
                    {{ __('messages.previous') }}
                </x-secondary-button>
                <x-primary-button x-on:click.prevent="checkClash()">
                    {{ __('messages.next') }}
                </x-primary-button>
            </div>
        </div>

        <!-- Step 3: Booking Details -->
        <div x-show="currentStep === 3">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                {{ __('Booking Details') }}
            </h2>

            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400 mb-6">
                {{ __('Please provide additional details for your booking.') }}
            </p>

            <div class="space-y-4">
                <div>
                    <x-input-label for="number_of_students" :value="__('Number of Students')" />
                    <x-text-input id="number_of_students" name="number_of_student" type="number"
                        class="mt-1 block w-full" x-model="numberOfStudents" required />
                </div>
                <div>
                    <x-input-label for="equipment_needed" :value="__('Equipment Needed (Optional)')" />
                    <textarea id="equipment_needed" name="equipment_needed" rows="3"
                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                        x-model="equipmentNeeded"></textarea>
                </div>
                <div>
                    <x-input-label for="purpose" :value="__('Purpose')" />
                    <textarea id="purpose" name="purpose" rows="3"
                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                        x-model="purpose" required></textarea>
                </div>
            </div>

            <div class="mt-6 flex justify-end space-x-3">
                <x-secondary-button x-on:click.prevent="currentStep = 2">
                    {{ __('messages.previous') }}
                </x-secondary-button>
                <x-primary-button type="submit">
                    {{ __('messages.save') }}
                </x-primary-button>
            </div>
        </div>
    </form>
</div>
