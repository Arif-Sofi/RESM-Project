<a href="#" x-on:click.prevent="$dispatch('open-modal', 'reject-modal-{{ $booking->id }}');"
    class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-600 ml-1 mr-1">{{ __('Reject') }}
</a>

<x-modal name="reject-modal-{{ $booking->id }}">
    <div class="">
        <div class="bg-base dark:bg-primary rounded shadow p-6">
            <p class="mb-2 text-center text-primary dark:text-base">
                {{ __('Are you sure you want to reject this booking?') }}
            </p>
            <form action="{{ route('bookings.reject', $booking) }}" method="POST" class="mt-6">
                @csrf
                @method('PATCH')

                <x-input-label for="reason_reject" :value="__('Reason for Rejection')" class="mb-2 text-primary dark:text-base"/>
                <textarea id="reason_reject" name="reason_reject" rows="3"
                    class="mt-1 block w-full border-secondary dark:border-accent bg-white dark:bg-primary text-primary dark:text-base focus:border-primary dark:focus:border-indigo-600 focus:ring-primary dark:focus:ring-indigo-600 rounded-md shadow-sm"
                    x-model="reasonReject" required>
                </textarea>

                <div class="mt-4 flex justify-center">
                    <button type="submit"
                        class="mr-8 px-4 py-2 bg-red-600 text-white rounded">{{ __('Reject') }}
                    </button>
                    <button type="button" x-on:click="$dispatch('close')"
                        class="ml-8 px-4 py-2 bg-accent rounded">{{ __('messages.cancel') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-modal>
