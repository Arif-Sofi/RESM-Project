<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('navigation.bookings') }}
        </h2>
    </x-slot>

    <div class="w-full py-12">
        <div class="w-full max-w-[1920px] mx-auto px-4 sm:px-6 lg:px-8">
            <x-primary-button x-data="" x-on:click.prevent="$dispatch('open-modal', 'room-selection-modal')">
                {{ __('Room') }}
            </x-primary-button>
            <x-secondary-button class="ms-2" x-data="" x-on:click.prevent="$dispatch('open-modal', 'booking-date-time-modal')">
                {{ __('Date') }}
            </x-secondary-button>
        </div>
    </div>

    @include('bookings._room_modal')
    @include('bookings._booking_modal')
</x-app-layout>
