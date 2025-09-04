<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('navigation.bookings') }}
        </h2>
    </x-slot>
    <div class="w-full py-12">
        <div class="w-full max-w-[1920px] mx-auto px-4 sm:px-6 lg:px-8" x-data="dateBookingFlow()">
            <!-- Container for the entire inline booking flow -->
            <div x-data="roomBookingFlow()">
                <!-- Booking Type Selection Buttons -->
                <div class="mb-8">
                    <x-primary-button x-on:click.prevent="showRoomBooking()">
                        {{ __('messages.room') }}
                    </x-primary-button>
                    <x-secondary-button class="ms-2"
                        x-on:click.prevent="showDateBooking()">
                        {{ __('messages.date') }}
                    </x-secondary-button>
                </div>

                <!-- Room Booking Content -->
                @include('bookings._room_modal')
            </div>
            <!-- Date Booking Content -->
            @include('bookings._date_modal')

            <!-- Bookings Table -->
            @include('bookings._table_modal')
        </div>
    </div>


</x-app-layout>
