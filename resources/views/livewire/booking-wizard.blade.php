<div x-data="{}">
    <form wire:submit.prevent="submitBooking" class="p-6">
        <input type="hidden" name="room_id" wire:model="selectedRoomId">
        {{-- start_time and end_time are calculated in Livewire component, no need for hidden inputs here --}}

        {{-- Step 1: Display Previous Bookings & Date/Time Selection --}}
        <div x-show="$wire.currentStep === 1">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                {{ __('Select Date and Time') }}
            </h2>

            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                {{ __('Previous bookings for this room:') }}
            </p>

            <div class="mt-4 max-h-40 overflow-y-auto border p-2 rounded">
                @if (count($previousBookings) > 0)
                    <ul>
                        @foreach ($previousBookings as $booking)
                            <li class="text-sm text-gray-700 dark:text-gray-300">
                                {{ \Carbon\Carbon::parse($booking->start_time)->toLocaleString() }} - {{ \Carbon\Carbon::parse($booking->end_time)->toLocaleString() }}
                                ({{ $booking->purpose }})
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-sm text-gray-500">{{ __('No previous bookings found for this room.') }}</p>
                @endif
            </div>

            <div class="mt-6">
                <div class="mb-4">
                    <x-input-label for="booking_date" :value="__('Date')" />
                    <x-text-input id="booking_date" name="booking_date_step1" type="date" class="mt-1 block w-full" wire:model.live="selectedDate" />
                    @error('selectedDate') <span class="text-sm text-red-600 dark:text-red-400 mt-2">{{ $message }}</span> @enderror
                </div>
                <div class="mb-4">
                    <x-input-label for="booking_time" :value="__('Time')" />
                    <x-text-input id="booking_time" name="booking_time_step1" type="time" class="mt-1 block w-full" wire:model.live="selectedTime" />
                    @error('selectedTime') <span class="text-sm text-red-600 dark:text-red-400 mt-2">{{ $message }}</span> @enderror
                </div>
                <p x-show="$wire.clashDetected" class="text-sm text-red-600 dark:text-red-400 mt-2" x-text="$wire.clashMessage"></p>
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('messages.cancel') }}
                </x-secondary-button>
                <x-primary-button class="ms-3" wire:click="checkClash">
                    {{ __('messages.next') }}
                </x-primary-button>
            </div>
        </div>

        {{-- Step 2: Input Booking Details --}}
        <div x-show="$wire.currentStep === 2">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                {{ __('Booking Details') }}
            </h2>

            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                {{ __('Please provide additional details for your booking.') }}
            </p>

            <div class="mt-6">
                <div class="mb-4">
                    <x-input-label for="number_of_students" :value="__('Number of Students')" />
                    <x-text-input id="number_of_students" name="number_of_student" type="number" class="mt-1 block w-full" wire:model="numberOfStudents" required />
                    @error('numberOfStudents') <span class="text-sm text-red-600 dark:text-red-400 mt-2">{{ $message }}</span> @enderror
                </div>
                <div class="mb-4">
                    <x-input-label for="equipment_needed" :value="__('Equipment Needed (Optional)')" />
                    <textarea id="equipment_needed" name="equipment_needed" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" wire:model="equipmentNeeded"></textarea>
                    @error('equipmentNeeded') <span class="text-sm text-red-600 dark:text-red-400 mt-2">{{ $message }}</span> @enderror
                </div>
                <div class="mb-4">
                    <x-input-label for="purpose" :value="__('Purpose')" />
                    <textarea id="purpose" name="purpose" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" wire:model="purpose" required></textarea>
                    @error('purpose') <span class="text-sm text-red-600 dark:text-red-400 mt-2">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button wire:click="goToStepOne">
                    {{ __('messages.back') }}
                </x-secondary-button>
                <x-primary-button class="ms-3" type="submit">
                        {{ __('messages.save') }}
                </x-primary-button>
            </div>
        </div>
    </form>
</div>
