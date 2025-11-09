<a href="#" x-on:click.prevent="$dispatch('open-modal', 'approve-modal-{{ $booking->id }}');"
    class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-600 ml-1 mr-1">{{ __('Approve') }}
</a>

<x-modal name="approve-modal-{{ $booking->id }}">
    <div class="">
        <div class="bg-white dark:bg-gray-800 rounded shadow p-6">
            <p class="mb-4 text-center">
                {{ __('Are you sure you want to approve this booking?') }}
            </p>
            <form action="{{ route('bookings.approve', $booking) }}" method="POST" class="flex justify-center">
                @csrf
                @method('PATCH')
                <button type="submit"
                    class="mr-8 px-4 py-2 bg-green-600 text-white rounded">{{ __('Approve') }}
                </button>
                <button type="button" x-on:click="$dispatch('close')"
                    class="ml-8 px-4 py-2 bg-gray-300 rounded">{{ __('messages.cancel') }}
                </button>
            </form>
        </div>
    </div>
</x-modal>
