@component('mail::message')
# New Event Created

A new event has been created: {{ $event->title }}

@component('mail::button', ['url' => url('/events/' . $event->id)])
View Event
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
