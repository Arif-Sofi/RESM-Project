<x-modal name="room-selection-modal" :show="$errors->roomSelection->isNotEmpty()" focusable>
    <div class="p-6">
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Select a Room') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __('Choose a room from the list below.') }}
        </p>

        <div class="mt-6">
            @foreach ($rooms as $room)
                <label class="block">
                    <input type="radio" name="selected_room" value="{{ $room->id }}"> {{ $room->name }}
                    <span class="text-gray-500 text-sm block pl-10">{{ $room->description }}</span>
                    <span class="text-gray-500 text-sm block pl-10">{{ $room->location_details }}</span>
                    <hr>
                </label>
            @endforeach

        </div>

        <div class="mt-6 flex justify-end">
            <x-secondary-button x-on:click="$dispatch('close')">
                {{ __('messages.cancel') }}
            </x-secondary-button>
            <x-primary-button class="ms-3" x-on:click="
                const selectedRoom = document.querySelector('input[name=selected_room]:checked');
                if (selectedRoom) {
                    $dispatch('open-booking-details-modal', { roomId: selectedRoom.value });
                    $dispatch('close');
                } else {
                    alert('Please select a room first.');
                }
            ">
                {{ __('Next') }}
            </x-primary-button>
        </div>
    </div>
</x-modal>
