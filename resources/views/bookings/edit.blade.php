<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-primary dark:text-base leading-tight">
            {{ __('Edit Booking') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-base dark:bg-primary overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-primary dark:text-base">
                    <form method="POST" action="{{ route('bookings.update', $booking) }}">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="start_time"
                            :value="selectedDate && selectedStartTime ? new Date(selectedDate + 'T' + selectedStartTime + ':00')
                            .toISOString().slice(0, 19).replace('T', ' ') : ''">
                        <!-- 2025-10-27T14:30:00.000Z converts to 2025-10-27 14:30:00-->
                        <input type="hidden" name="end_time"
                            :value="selectedDate && selectedEndTime ? new Date(selectedDate + 'T' + selectedEndTime + ':00')
                            .toISOString().slice(0, 19).replace('T', ' ') : ''">

                        <!-- Room Selection -->
                        <div>
                            <x-input-label for="room_id" :value="__('Room')" />
                            <select id="room_id" name="room_id" class="block mt-1 w-full">
                                @foreach($rooms as $room)
                                    <option value="{{ $room->id }}" {{ $booking->room_id == $room->id ? 'selected' : '' }}>
                                        {{ $room->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mt-4">
                            <x-input-label for="booking_date" :value="__('Date')" />
                            <x-text-input id="booking_date" class="block mt-1 w-full" type="date" name="booking_date" x-model="selectedDate"
                            min="{{ now()->toDateString() }}" :value="old('booking_date', $booking->start_time->format('Y-m-d'))" required />
                        </div>

                        <!-- Date and Time -->
                        <div class="mt-4">
                            <x-input-label for="start_time" :value="__('Start Time')" />
                            <x-text-input id="start_time" class="block mt-1 w-full" type="time" name="start_time" x-model="selectedStartTime"
                             :value="old('start_time', $booking->start_time->format('H:i'))" required />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="end_time" :value="__('End Time')" />
                            <x-text-input id="end_time" class="block mt-1 w-full" type="time" name="end_time" x-model="selectedEndTime"
                             :value="old('end_time', $booking->end_time->format('H:i'))" required />
                        </div>

                        <!-- Number of Students -->
                        <div class="mt-4">
                            <x-input-label for="number_of_student" :value="__('Number of Students')" />
                            <x-text-input id="number_of_student" class="block mt-1 w-full" type="number" name="number_of_student"
                            min="0" :value="old('number_of_student', $booking->number_of_student)" />
                        </div>

                        <!-- Equipment Needed -->
                        <div class="mt-4">
                            <x-input-label for="equipment_needed" :value="__('Equipment Needed')" />
                            <x-text-input id="equipment_needed" class="block mt-1 w-full" type="text" name="equipment_needed" :value="old('equipment_needed', $booking->equipment_needed)" />
                        </div>

                        <!-- Purpose -->
                        <div class="mt-4">
                            <x-input-label for="purpose" :value="__('Purpose')" />
                            <textarea id="purpose" name="purpose" class="block mt-1 w-full">{{ old('purpose', $booking->purpose) }}</textarea>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-secondary-button onclick="window.location.href='{{ route('bookings.index') }}'">
                                {{ __('Cancel') }}
                            </x-secondary-button>
                            <x-primary-button class="ml-4">
                                {{ __('Update Booking') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
