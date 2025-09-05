<x-mail::message>
# Booking Approved

Your booking for **{{ $booking->room->name }}** from **{{ $booking->start_time }}**
to **{{ $booking->end_time }}** has been approved.

Thank you for using our service.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
