@component('mail::message')
# Event Reminder

Your event **{{ $event->title }}** is starting in 1 hour.

**Start Time:** {{ $event->start_at->format('F j, Y g:i A') }}

@if($event->end_at)
**End Time:** {{ $event->end_at->format('F j, Y g:i A') }}
@endif

@if($event->description)
**Description:** {{ $event->description }}
@endif

@component('mail::button', ['url' => url('/events')])
View Events
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
