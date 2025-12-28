<x-mail::message>
# New Booking Request

A new booking request has been submitted and is waiting for your approval.

**Requester:** {{ $booking->user->name }} ({{ $booking->user->email }})
**Room:** {{ $booking->room->name }}
**Date:** {{ $booking->start_time->format('Y-m-d') }}
**Time:** {{ $booking->start_time->format('H:i') }} - {{ $booking->end_time->format('H:i') }}
**Purpose:** {{ $booking->purpose }}

<x-mail::button :url="route('admin.approvals')">
Review Request
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
