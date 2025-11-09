<a href="#" x-on:click.prevent="$dispatch('open-modal', 'reject-modal-{{ $booking->id }}');"
    class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-600 ml-1 mr-1">{{ __('Reject') }}
</a>

<x-modal name="reject-modal-{{ $booking->id }}">
    <div class="">
        <div class="bg-white dark:bg-gray-800 rounded shadow p-6">
            <p class="mb-4 text-center">
                {{ __('Are you sure you want to reject this booking?') }}
            </p>
            <form action="{{ route('bookings.reject', $booking) }}" method="POST" class="flex justify-center">
                @csrf
                @method('PATCH')
                <button type="submit"
                    class="mr-8 px-4 py-2 bg-red-600 text-white rounded">{{ __('Reject') }}
                </button>
                <button type="button" x-on:click="$dispatch('close')"
                    class="ml-8 px-4 py-2 bg-gray-300 rounded">{{ __('messages.cancel') }}
                </button>
            </form>
        </div>
    </div>
</x-modal>
