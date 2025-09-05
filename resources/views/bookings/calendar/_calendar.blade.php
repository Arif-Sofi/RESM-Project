
<div class="container mx-auto p-4">
    <h2 class="text-2xl font-bold mb-4">予約カレンダー</h2>
    <div id="calendar"></div>
</div>

@push('scripts')
    @include('bookings.calendar._js')
@endpush
