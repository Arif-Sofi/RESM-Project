    <a href="#" x-on:click.prevent="$dispatch('open-modal', 'delete-modal-{{ $booking->id }}');"
    class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-600 ml-1">{{ __('Delete') }}
</a>

<x-modal name="delete-modal-{{ $booking->id }}">
    <div class="">
        <div class="bg-white dark:bg-gray-800 rounded shadow p-6">
            <p class="mb-4">
                {{ __('This action cannot be undone. Are you sure you want to delete this?') }}
            </p>
            <form action="{{ route('bookings.destroy', $booking) }}" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit"
                    class="mr-2 px-4 py-2 bg-red-600 text-white rounded">{{ __('Confirm') }}
                </button>
                <button type="button" x-on:click="$dispatch('close')"
                    class="px-4 py-2 bg-gray-300 rounded">{{ __('Cancel') }}
                </button>
            </form>
        </div>
    </div>
</x-modal>
