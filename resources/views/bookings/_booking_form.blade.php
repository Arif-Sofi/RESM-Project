<div>
    <div class="flex justify-between items-center mb-6">
        <div>
            <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ __('Create New Booking') }}</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ __('Fill in the details below to book a room') }}</p>
        </div>
        <button @click="showBookingForm = false; selectedRoom = null; selectedDate = null;" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    <form action="{{ route('bookings.store') }}" method="POST" class="space-y-6">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Room Selection -->
            <div>
                <label for="room_id" class="block font-medium text-sm text-gray-700 dark:text-gray-300 mb-2">
                    {{ __('Room') }} <span class="text-red-500">*</span>
                </label>
                <!-- Hidden input for room_id that gets submitted -->
                <input type="hidden" name="room_id" :value="typeof selectedRoom === 'object' ? selectedRoom?.id : selectedRoom">
                <select id="room_id_display" @change="selectedRoom = parseInt($event.target.value); checkAvailability()" required
                    :value="typeof selectedRoom === 'object' ? selectedRoom?.id : selectedRoom"
                    class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary dark:focus:border-primary focus:ring-primary dark:focus:ring-primary rounded-md shadow-sm w-full">
                    <option value="">{{ __('Select a room') }}</option>
                    <template x-for="room in rooms" :key="room.id">
                        <option :value="room.id" x-text="`${room.name} - ${room.location_details || ''}`"></option>
                    </template>
                </select>
                @error('room_id')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Date Selection -->
            <div>
                <label for="booking_date_display" class="block font-medium text-sm text-gray-700 dark:text-gray-300 mb-2">
                    {{ __('Date') }} <span class="text-red-500">*</span>
                </label>
                <input type="date" id="booking_date_display" x-model="selectedDate" @change="updateTimes(); checkAvailability()" required
                    min="{{ date('Y-m-d') }}"
                    class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary dark:focus:border-primary focus:ring-primary dark:focus:ring-primary rounded-md shadow-sm w-full">
                @error('start_time')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Start Time -->
            <div>
                <label for="start_time_display" class="block font-medium text-sm text-gray-700 dark:text-gray-300 mb-2">
                    {{ __('Start Time') }} <span class="text-red-500">*</span>
                </label>
                <!-- Hidden input that sends the full datetime -->
                <input type="hidden" name="start_time" :value="selectedStartTime">
                <input type="time" id="start_time_display" x-bind:value="formatTimeForInput(selectedStartTime)" @change="updateStartTime($event); checkAvailability()" required
                    class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary dark:focus:border-primary focus:ring-primary dark:focus:ring-primary rounded-md shadow-sm w-full">
                @error('start_time')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- End Time -->
            <div>
                <label for="end_time_display" class="block font-medium text-sm text-gray-700 dark:text-gray-300 mb-2">
                    {{ __('End Time') }} <span class="text-red-500">*</span>
                </label>
                <!-- Hidden input that sends the full datetime -->
                <input type="hidden" name="end_time" :value="selectedEndTime">
                <input type="time" id="end_time_display" x-bind:value="formatTimeForInput(selectedEndTime)" @change="updateEndTime($event); checkAvailability()" required
                    class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary dark:focus:border-primary focus:ring-primary dark:focus:ring-primary rounded-md shadow-sm w-full">
                @error('end_time')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Number of Students -->
            <div>
                <label for="number_of_student" class="block font-medium text-sm text-gray-700 dark:text-gray-300 mb-2">
                    {{ __('Number of Students') }} <span class="text-red-500">*</span>
                </label>
                <input type="number" id="number_of_student" name="number_of_student" min="1" max="100" required
                    class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary dark:focus:border-primary focus:ring-primary dark:focus:ring-primary rounded-md shadow-sm w-full">
                @error('number_of_student')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Equipment Needed -->
            <div>
                <label for="equipment_needed" class="block font-medium text-sm text-gray-700 dark:text-gray-300 mb-2">
                    {{ __('Equipment Needed') }}
                </label>
                <input type="text" id="equipment_needed" name="equipment_needed" placeholder="{{ __('e.g., Projector, Whiteboard, Computer') }}"
                    class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary dark:focus:border-primary focus:ring-primary dark:focus:ring-primary rounded-md shadow-sm w-full">
                @error('equipment_needed')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Purpose (Full Width) -->
        <div>
            <label for="purpose" class="block font-medium text-sm text-gray-700 dark:text-gray-300 mb-2">
                {{ __('Purpose') }} <span class="text-red-500">*</span>
            </label>
            <textarea id="purpose" name="purpose" rows="3" required placeholder="{{ __('Describe the purpose of this booking...') }}"
                class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary dark:focus:border-primary focus:ring-primary dark:focus:ring-primary rounded-md shadow-sm w-full"></textarea>
            @error('purpose')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <!-- Clash Error Display -->
        <div x-show="clashError" x-transition class="p-4 bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 rounded">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-red-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                <p class="text-sm text-red-700 dark:text-red-300" x-text="clashError"></p>
            </div>
        </div>

        <!-- Laravel Validation Errors -->
        @if($errors->any())
        <div class="p-4 bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 rounded">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-red-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                <div>
                    <p class="text-sm font-semibold text-red-700 dark:text-red-300">{{ __('Please fix the following errors:') }}</p>
                    <ul class="mt-2 text-sm text-red-700 dark:text-red-300 list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        @endif

        <!-- Action Buttons -->
        <div class="flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
            <button type="button" @click="showBookingForm = false; selectedRoom = null; selectedDate = null;"
                class="inline-flex items-center px-6 py-3 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 transition">
                {{ __('Cancel') }}
            </button>
            <button type="submit" :disabled="!!clashError"
                class="inline-flex items-center px-6 py-3 bg-primary border border-transparent rounded-md font-semibold text-sm text-white hover:bg-opacity-90 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 transition disabled:opacity-50 disabled:cursor-not-allowed">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                {{ __('Create Booking') }}
            </button>
        </div>
    </form>
</div>
