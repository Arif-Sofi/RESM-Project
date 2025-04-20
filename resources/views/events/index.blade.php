@extends('events.layout')

@section('title', 'イベントカレンダー')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold mb-6">カレンダー</h1>

        {{-- ここにタイムラインやフィルタリングなどの要素を配置する --}}
        {{-- <div class="timeline-section"> ... </div> --}}

        {{-- FullCalendarを表示するためのHTML要素 --}}
        <div id='calendar'></div>

    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');

            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'ja',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },

                events: '#{{-- route('api.events') --}}',

                eventClick: function(info) {
                    if (info.event.url) {
                        window.open(info.event.url);
                    } else {
                        alert('Event Details:\n' +
                            'Title: ' + info.event.title + '\n' +
                            'Start: ' + info.event.start.toLocaleString() +
                            (info.event.end ? '\nEnd: ' + info.event.end.toLocaleString() : '')
                        );
                    }
                    info.jsEvent.preventDefault(); // デフォルトのリンク動作などをキャンセル
                },

                // 日付部分をクリックした時のコールバック関数
                dateClick: function(info) {
                    alert('Date: ' + info.dateStr);
                    // 例: window.location.href = '{{ route('events.create') }}?date=' + info.dateStr;
                },

                // FullCalendar の設定オプション
                // https://fullcalendar.io/docs
            });

            calendar.render();
        });
    </script>
@endpush
