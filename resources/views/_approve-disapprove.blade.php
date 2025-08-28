@if (auth()->user()->isAdmin() AND (!$booking->status OR $booking->status === null))
    <form action="{{ route('booking.approve', $booking) }}" method="POST">
        @csrf
        <x-primary-button type="submit" class="h-12 w-28 justify-center">
            Approve
        </x-primary-button>
    </form>
@endif
@if (auth()->user()->isAdmin() AND ($booking->status OR $booking->status === null))
    <form action="{{ route('booking.disapprove', $booking) }}" method="POST">
        @csrf
        <x-secondary-button type="submit" class="h-12 w-28 justify-center">
            Disapprove
        </x-secondary-button>
    </form>
@endif
