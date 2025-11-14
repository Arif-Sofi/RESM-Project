    <a href="#" x-on:click.prevent="$dispatch('open-modal', 'delete-modal-{{ $booking->id }}');"
    class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-600 ml-1">{{ __('Cancel') }}
</a>

<x-modal name="delete-modal-{{ $booking->id }}">
    <div class="">
        <div class="bg-base dark:bg-gray-800 rounded shadow p-6">
            <p class="mb-4 text-center">
                THIS ACTION CANNOT BE UNDONE. <br>Are you sure you want to Cancel booking?
            </p>
            <form action="{{ route('bookings.destroy', $booking) }}" method="POST" class="flex justify-center">
                @csrf
                @method('DELETE')
                <button type="submit"
                    class="mr-8 px-4 py-2 bg-red-600 text-white rounded">{{ __('messages.confirm') }}
                </button>
                <button type="button" x-on:click="$dispatch('close')"
                    class="ml-8 px-4 py-2 bg-gray-300 rounded">{{ __('messages.cancel') }}
                </button>
            </form>
        </div>
    </div>
</x-modal>
