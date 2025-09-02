@if (auth()->user()->isAdmin() AND ($booking->status === null))
    <form action="{{ route('bookings.approve', $booking) }}" method="POST">
        @csrf
        @method('PATCH')
        <x-primary-button type="submit" class="h-12 w-28 mb-4 justify-center">
            Approve
        </x-primary-button>
    </form>
@endif
@if (auth()->user()->isAdmin() AND ($booking->status === null))
    <form action="{{ route('bookings.reject', $booking) }}" method="POST">
        @csrf
        @method('PATCH')
        <x-secondary-button type="submit" class="h-12 w-28 justify-center">
            Disapprove
        </x-secondary-button>
    </form>
@endif
