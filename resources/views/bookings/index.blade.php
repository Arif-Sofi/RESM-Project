<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('navigation.bookings') }}
        </h2>
    </x-slot>
    @include('bookings._room_booking_modal')
    @include('bookings._date_booking_modal')
</x-app-layout>
