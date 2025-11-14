<div class="mt-8" x-data="{
    viewBookingData: {
        id: '',
        room_name: '',
        user_name: '',
        user_id: '',
        number_students: '',
        equiptment_needed: '',
        purpose: '',
        start_time: '',
        end_time: '',
        status: ''
    },
    setViewBookingData(booking) {
        this.viewBookingData.id = booking.id;
        this.viewBookingData.room_name = booking.room ? booking.room.name : 'N/A';
        this.viewBookingData.user_name = booking.user ? booking.user.name : 'N/A';
        this.viewBookingData.user_id = booking.user_id;
        this.viewBookingData.number_students = booking.number_of_student;
        this.viewBookingData.equiptment_needed = booking.equipment_needed;
        this.viewBookingData.purpose = booking.purpose;
        this.viewBookingData.start_time = new Date(booking.start_time).toLocaleString([], { dateStyle: 'short', timeStyle: 'short' });
        this.viewBookingData.end_time = new Date(booking.end_time).toLocaleTimeString([], { timeStyle: 'short' });
        this.viewBookingData.status = booking.status;
        $dispatch('open-modal', 'view-booking-modal');
    }
}">
    <h3 class="text-lg font-semibold text-primary dark:text-base leading-tight">
        {{ auth()->user()->isAdmin() ? __('All Bookings') : __('My Bookings') }}
    </h3>
    <div class="bg-lightbase dark:bg-primary shadow-sm sm:rounded-lg mt-4 ring-secondary ring-1">
        <!-- Desktop Header -->
        <div class="hidden rounded-t-lg md:grid @if(auth()->user()->isAdmin()) md:grid-cols-6 @else md:grid-cols-5 @endif gap-4 px-6 py-3 bg-accent dark:bg-secondary text-left text-xs font-medium text-primary dark:text-base uppercase tracking-wider">
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
        <div class="divide-y divide-secondary dark:divide-accent">
            @forelse ($bookings as $booking)
                @if($booking->status === NULL)
                    <div class="p-4 grid grid-cols-2 md:@if(auth()->user()->isAdmin())grid-cols-6 @else grid-cols-5 @endif gap-4 items-center">
                        <!-- Room -->
                        <div class="col-span-2 md:col-span-1 order-1 md:order-none">
                            <div class="md:hidden font-bold text-primary dark:text-base">{{ __('Room') }}</div>
                            <div class="text-sm font-medium text-primary dark:text-base">{{ $booking->room->name }}</div>
                        </div>

                        <!-- User (Admin only) -->
                        @if (auth()->user()->isAdmin())
                            <div class="col-span-2 md:col-span-1 order-2 md:order-none">
                                <div class="md:hidden font-bold text-primary dark:text-base">{{ __('User') }}</div>
                                <div class="text-sm text-primary dark:text-base">{{ $booking->user->name }}</div>
                            </div>
                        @endif

                        <!-- Start Time -->
                        <div class="col-span-1 order-3 md:order-none">
                            <div class="md:hidden font-bold text-primary dark:text-base">{{ __('Start Time') }}</div>
                            <div class="text-sm text-primary dark:text-base">{{ $booking->start_time->format('Y-m-d H:i') }}</div>
                        </div>

                        <!-- End Time -->
                        <div class="col-span-2 md:col-span-1 order-5 md:order-none">
                            <div class="md:hidden font-bold text-primary dark:text-base">{{ __('End Time') }}</div>
                            <div class="text-sm text-primary dark:text-base">{{ $booking->end_time->format('Y-m-d H:i') }}</div>
                        </div>

                        <!-- Status -->
                        <div class="col-span-2 md:col-span-1 order-6 md:order-none">
                            <div class="md:hidden font-bold text-primary dark:text-base">{{ __('Status') }}</div>
                            <div class="text-sm text-yellow-500 dark:text-yellow-400">{{ $booking->status ?? 'Pending' }}</div>
                        </div>

                        <!-- Actions -->
                        <div class="col-span-1 md:col-span-1 order-4 md:order-none">
                            <div class="md:hidden font-bold text-primary dark:text-base">{{ __('Actions') }}</div>
                            <div class="text-sm font-medium flex space-x-2">
                                <div style="display:flex flex-wrap:wrap">
                                <a href="#" x-on:click.prevent="setViewBookingData({{ json_encode($booking) }})" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-600 mr-1">{{ __('View') }}</a>
                                @if (auth()->user()->isAdmin())
                                    @include('bookings._approve_modal', ['booking' => $booking])
                                    @include('bookings._reject_modal', ['booking' => $booking])
                                @endif
                                @include('bookings._delete_modal', ['booking' => $booking])
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @empty
                <div class="p-4 text-center text-primary dark:text-base">
                    No pending bookings found.
                </div>
            @endforelse
        </div>
    </div>

    @include('bookings._view_modal')
</div>
