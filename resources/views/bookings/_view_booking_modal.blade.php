<x-modal name="view-booking-modal" :show="$errors->any() || session('status')">
    <div class="p-6">
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
            {{ __('messages.description') }}
        </h2>

        @if (session('status'))
            <div class="mb-4 p-3 bg-green-100 dark:bg-green-900 border border-green-300 dark:border-green-700 rounded-lg">
                <p class="text-sm text-green-600 dark:text-green-400">{{ session('status') }}</p>
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-4 p-3 bg-red-100 dark:bg-red-900 border border-red-300 dark:border-red-700 rounded-lg">
                <ul class="list-disc list-inside text-sm text-red-600 dark:text-red-400">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <x-input-label for="room_name" :value="__('Room')" />
                <p id="room_name" class="mt-1 text-gray-900 dark:text-gray-100" x-text="viewBookingData.room_name"></p>
            </div>
            <div>
                <x-input-label for="user_name" :value="__('User')" />
                <p id="user_name" class="mt-1 text-gray-900 dark:text-gray-100" x-text="viewBookingData.user_name"></p>
            </div>
            <div>
                <x-input-label for="number_students" :value="__('Number of Students')" />
                <p id="number_students" class="mt-1 text-gray-900 dark:text-gray-100" x-text="viewBookingData.number_students"></p>
            </div>
            <div>
                <x-input-label for="equipment_needed" :value="__('Equipment Needed')" />
                <p id="equipment_needed" class="mt-1 text-gray-900 dark:text-gray-100" x-text="viewBookingData.equiptment_needed ? viewBookingData.equiptment_needed : '{{ __('None') }}'"></p>
            </div>
            <div>
                <x-input-label for="purpose" :value="__('Purpose')" />
                <p id="purpose" class="mt-1 text-gray-900 dark:text-gray-100" x-text="viewBookingData.purpose"></p>
            </div>
            <div>
                <x-input-label for="start_time" :value="__('Time')" />
                <span id="start_time" class="mt-1 text-gray-900 dark:text-gray-100" x-text="viewBookingData.start_time"></span>
                -
                <span id="end_time" class="mt-1 text-gray-900 dark:text-gray-100" x-text="viewBookingData.end_time"></span>
            </div>
        </div>
    </div>
</x-modal>
