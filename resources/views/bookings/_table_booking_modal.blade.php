<div class="mt-8" x-data="{
    viewBookingData: {
        room_name: '',
        user_name: '',
        number_students: '',
        equiptment_needed: '',
        purpose: '',
        start_time: '',
        end_time: ''
    },
    setViewBookingData(booking) {
        this.viewBookingData.room_name = booking.room ? booking.room.name : 'N/A';
        this.viewBookingData.user_name = booking.user ? booking.user.name : 'N/A';
        this.viewBookingData.number_students = booking.number_of_student;
        this.viewBookingData.equiptment_needed = booking.equipment_needed;
        this.viewBookingData.purpose = booking.purpose;
        this.viewBookingData.start_time = new Date(booking.start_time).toISOString().slice(0, 16).replace('T', ' ');
        this.viewBookingData.end_time = new Date(booking.end_time).toISOString().slice(10, 16).replace('T', ' ');
        $dispatch('open-modal', 'view-booking-modal');
    }
}">
    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 leading-tight">
        {{ auth()->user()->isAdmin() ? __('All Bookings') : __('My Bookings') }}
    </h3>
    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg mt-4">
        <!-- Desktop Header -->
        <div class="hidden md:grid @if(auth()->user()->isAdmin()) md:grid-cols-6 @else md:grid-cols-5 @endif gap-4 px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
            <div class="col-span-1">{{ __('Room') }}</div>
            @if (auth()->user()->isAdmin())
                <div class="col-span-1">{{ __('User') }}</div>
            @endif
            <div class="col-span-1">{{ __('Start Time') }}</div>
            <div class="col-span-1">{{ __('End Time') }}</div>
            <div class="col-span-1">{{ __('Status') }}</div>
            <div class="col-span-1">{{ __('Actions') }}</div>
        </div>

        <!-- Booking List -->
        <div class="divide-y divide-gray-200 dark:divide-gray-700">
            @forelse ($bookings as $booking)
                @if($booking->status === NULL)
                    <div class="p-4 grid grid-cols-2 md:@if(auth()->user()->isAdmin())grid-cols-6 @else grid-cols-5 @endif gap-4 items-center">
                        <!-- Room -->
                        <div class="col-span-2 md:col-span-1 order-1 md:order-none">
                            <div class="md:hidden font-bold text-gray-500 dark:text-gray-400">{{ __('Room') }}</div>
                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $booking->room->name }}</div>
                        </div>

                        <!-- User (Admin only) -->
                        @if (auth()->user()->isAdmin())
                            <div class="col-span-2 md:col-span-1 order-2 md:order-none">
                                <div class="md:hidden font-bold text-gray-500 dark:text-gray-400">{{ __('User') }}</div>
                                <div class="text-sm text-gray-500 dark:text-gray-300">{{ $booking->user->name }}</div>
                            </div>
                        @endif

                        <!-- Start Time -->
                        <div class="col-span-1 order-3 md:order-none">
                            <div class="md:hidden font-bold text-gray-500 dark:text-gray-400">{{ __('Start Time') }}</div>
                            <div class="text-sm text-gray-500 dark:text-gray-300">{{ $booking->start_time->format('Y-m-d H:i') }}</div>
                        </div>

                        <!-- End Time -->
                        <div class="col-span-2 md:col-span-1 order-5 md:order-none">
                            <div class="md:hidden font-bold text-gray-500 dark:text-gray-400">{{ __('End Time') }}</div>
                            <div class="text-sm text-gray-500 dark:text-gray-300">{{ $booking->end_time->format('Y-m-d H:i') }}</div>
                        </div>

                        <!-- Status -->
                        <div class="col-span-2 md:col-span-1 order-6 md:order-none">
                            <div class="md:hidden font-bold text-gray-500 dark:text-gray-400">{{ __('Status') }}</div>
                            <div class="text-sm text-yellow-500 dark:text-yellow-400">{{ $booking->status ?? 'Pending' }}</div>
                        </div>

                        <!-- Actions -->
                        <div class="col-span-1 md:col-span-1 order-4 md:order-none">
                            <div class="md:hidden font-bold text-gray-500 dark:text-gray-400">{{ __('Actions') }}</div>
                            <div class="text-sm font-medium flex space-x-2">
                                <a href="#" x-on:click.prevent="setViewBookingData({{ json_encode($booking) }})" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-600">{{ __('View') }}</a>
                                @if (auth()->user()->isAdmin())
                                    <form action="{{ route('bookings.approve', $booking) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-600">{{ __('Approve') }}</button>
                                    </form>
                                    <form action="{{ route('bookings.reject', $booking) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-600">{{ __('Reject') }}</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            @empty
                <div class="p-4 text-center text-gray-500 dark:text-gray-400">
                    No pending bookings found.
                </div>
            @endforelse
        </div>
    </div>

    @include('bookings._view_booking_modal')
</div>
