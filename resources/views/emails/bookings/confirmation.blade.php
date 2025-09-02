<x-mail::message>
# Booking Confirmation

Your booking has been successfully created. Here is a Receipt of your booking.

**Booking Details:**
- Room: {{ $booking->room->name }}
- Number of Students: {{ $booking->number_of_student }}
- Equipment Needed: {{ $booking->equipment_needed }}
- Purpose: {{ $booking->purpose }}
- Start Time: {{ $booking->start_time }} ({{ config('app.timezone') }}- Timezone)
- End Time: {{ $booking->end_time }} ({{ config('app.timezone') }}- Timezone)

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
