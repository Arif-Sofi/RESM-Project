<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>

<div x-show="showRoomBookingFlow" x-transition class="bg-lightbase dark:bg-primary shadow-sm rounded-lg p-6 ring-secondary ring-1">
    <!-- Step 1: Room Selection -->
    <div x-show="currentStep === 1">
        <h2 class="text-lg font-medium text-primary dark:text-base mb-4">
            {{ __('messages.room_select') }}
        </h2>

        <p class="mt-1 text-sm text-primary dark:text-base mb-6">
            {{ __('messages.room_choose') }}
        </p>

        <div class="space-y-4">
            <div class="grid 2xl:grid-cols-6 xl:grid-cols-5 lg:grid-cols-4 md:grid-cols-3 sm:grid-cols-2 gap-4">
                @foreach ($rooms as $room)
                    <label
                        class="block cursor-pointer p-4 border bg-base border-secondary dark:border-accent rounded-lg hover:bg-accent dark:hover:bg-secondary transition-colors">
                        <div class="flex items-center">
                            <input type="radio" name="selected_room" value="{{ $room->id }}"
                                x-model="selectedRoomId" class="mr-4">
                            <div>
                                <span
                                    class="font-semibold text-primary dark:text-base text-lg">{{ $room->name }}</span>
                                <p class="text-primary dark:text-base text-sm mt-1">
                                    {{ $room->description }}</p>
                                <p class="text-primary dark:text-base text-sm">
                                    {{ $room->location_details }}</p>
                            </div>
                        </div>
                    </label>
                @endforeach
            </div>

            {{-- 既存予約の表示 --}}
            <div
                class="mb-6 max-h-40 overflow-y-auto border border-secondary dark:border-accent p-4 rounded-lg bg-accent dark:bg-secondary">
                <template x-if="previousBookings.length > 0">
                    <ul class="space-y-2">
                        <template x-for="booking in previousBookings" :key="booking.id">
                            <li
                                class="text-sm text-primary dark:text-base p-2 bg-base dark:bg-primary rounded border">
                                <div class="font-medium">
                                    <span
                                        x-text="new Date(booking.start_time).toLocaleString([], { dateStyle: 'short', timeStyle: 'short' })"></span>
                                    -
                                    <span
                                        x-text="new Date(booking.end_time).toLocaleTimeString([], { timeStyle: 'short' })"></span>
                                </div>
                                <div class="text-primary dark:text-base">
                                    Purpose: <span x-text="booking.purpose"></span>
                                </div>
                                <div class="text-primary dark:text-base text-xs">
                                    Booked by: <span x-text="booking.user ? booking.user.name : 'Unknown User'"></span>
                                </div>
                            </li>
                        </template>
                    </ul>
                </template>
                <template x-if="previousBookings.length === 0">
                    <p class="text-sm text-primary dark:text-base text-center py-4">
                        {{ __('Select a Room to see previous bookings.') }}</p>
                </template>
            </div>
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

    <!-- Previous Bookings Display -->
    <hr class="my-6 border-secondary dark:border-accent">
    <div id="calendar"></div>

    <!-- Step 2 & 3 Form -->
    <form method="POST" action="{{ route('bookings.store') }}" x-show="currentStep === 2 || currentStep === 3">
        @csrf
        <input type="hidden" name="room_id" x-model="selectedRoomId">
        <input type="hidden" name="start_time"
            :value="selectedDate && selectedStartTime ? new Date(selectedDate + 'T' + selectedStartTime + ':00')
                .toISOString().slice(0, 19).replace('T', ' ') : ''">
        <!-- 2025-10-27T14:30:00.000Z converts to 2025-10-27 14:30:00-->
        <input type="hidden" name="end_time"
            :value="selectedDate && selectedEndTime ? new Date(selectedDate + 'T' + selectedEndTime + ':00')
                .toISOString().slice(0, 19).replace('T', ' ') : ''">

        <!-- Step 2: Date and Time Selection -->
        <div x-show="currentStep === 2">
            <h2 class="text-lg font-medium text-primary dark:text-base mb-4">
                {{ __('messages.date_select') }}
            </h2>

            <p class="mt-1 text-sm text-primary dark:text-base mb-4">
                {{ __('messages.booking_previous') }}
            </p>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div>
                    <x-input-label for="booking_date" :value="__('Date')" />
                    <x-text-input id="booking_date" type="date" class="mt-1 block w-full" x-model="selectedDate" min="{{ now()->toDateString() }}"/>
                </div>
                <div>
                    <x-input-label for="start_time" :value="__('Start time')" />
                    <x-text-input id="start_time" type="time" class="mt-1 block w-full"
                        x-model="selectedStartTime" />
                </div>
                <div>
                    <x-input-label for="end_time" :value="__('End time')" />
                    <x-text-input id="end_time" type="time" class="mt-1 block w-full" x-model="selectedEndTime" />
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
            <h2 class="text-lg font-medium text-primary dark:text-base mb-4">
                {{ __('messages.booking_details') }}
            </h2>

            <p class="mt-1 text-sm text-primary dark:text-base mb-6">
                {{ __('messages.booking_additional_detail') }}
            </p>

            <div class="space-y-4">
                <div>
                    <x-input-label for="number_of_students" :value="__('Number of Students')" />
                    <x-text-input id="number_of_students" name="number_of_student" type="number"
                        class="mt-1 block w-full" x-model="numberOfStudents" min="0" required
                        @input="if ($event.target.value < 0) $event.target.value = 0;" />
                </div>
                <div>
                    <x-input-label for="equipment_needed" :value="__('Equipment Needed (Optional)')" />
                    <textarea id="equipment_needed" name="equipment_needed" rows="3"
                        class="mt-1 block w-full border-secondary dark:border-accent bg-base dark:bg-primary text-primary dark:text-base focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                        x-model="equipmentNeeded"></textarea>
                </div>
                <div>
                    <x-input-label for="purpose" :value="__('Purpose')" />
                    <textarea id="purpose" name="purpose" rows="3"
                        class="mt-1 block w-full border-secondary dark:border-accent bg-base dark:bg-primary text-primary dark:text-base focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
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
