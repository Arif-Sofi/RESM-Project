<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Booking') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST" action="{{ route('bookings.update', $booking) }}">
                        @csrf
                        @method('PATCH')

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

                        <!-- Date and Time -->
                        <div class="mt-4">
                            <x-input-label for="start_time" :value="__('Start Time')" />
                            <x-text-input id="start_time" class="block mt-1 w-full" type="datetime-local" name="start_time" :value="old('start_time', $booking->start_time->format('Y-m-d\TH:i'))" required />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="end_time" :value="__('End Time')" />
                            <x-text-input id="end_time" class="block mt-1 w-full" type="datetime-local" name="end_time" :value="old('end_time', $booking->end_time->format('Y-m-d\TH:i'))" required />
                        </div>

                        <!-- Number of Students -->
                        <div class="mt-4">
                            <x-input-label for="number_of_student" :value="__('Number of Students')" />
                            <x-text-input id="number_of_student" class="block mt-1 w-full" type="number" name="number_of_student" :value="old('number_of_student', $booking->number_of_student)" />
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
