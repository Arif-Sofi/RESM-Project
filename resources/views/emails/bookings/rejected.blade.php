<x-mail::message>
# Booking Update

We regret to inform you that your booking request for
**{{ $booking->room->name }}** on **{{ $booking->start_time }}**
has been rejected.

Below is the reason provided for the rejection:<br>
**{{ $booking->rejection_reason }}**

Please visit our platform to submit a new request.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
