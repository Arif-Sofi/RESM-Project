@extends('events.layout')

@section('title', 'イベントカレンダー')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold mb-6">カレンダー</h1>

        {{-- ここにタイムラインやフィルタリングなどの要素を配置する --}}
        {{-- <div class="timeline-section"> ... </div> --}}

        {{-- FullCalendarを表示するためのHTML要素 --}}
        <!-- モーダル表示に必要なCSS/JSを読み込む (例: Bootstrap) -->
        {{-- 必要に応じて、CDNまたはnpm/yarnでインストールしたファイルを読み込みます --}}
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

        <div id='calendar'></div>

        <!-- イベント追加用モーダル -->
        <div class="modal fade" id="createEventModal" tabindex="-1" aria-labelledby="createEventModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="createEventModalLabel">新しいイベントを追加</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="createEventForm">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="eventTitle" class="form-label">タイトル</label>
                                <input type="text" class="form-control" id="eventTitle" name="title" required>
                            </div>
                            <div class="mb-3">
                                <label for="eventDescription" class="form-label">説明</label>
                                <textarea class="form-control" id="eventDescription" name="description"></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="eventStart" class="form-label">開始日時</label>
                                <input type="datetime-local" class="form-control" id="eventStart" name="start_at" required>
                            </div>
                            <div class="mb-3">
                                <label for="eventEnd" class="form-label">終了日時 (任意)</label>
                                <input type="datetime-local" class="form-control" id="eventEnd" name="end_at">
                            </div>
                            <div class="mb-3">
                                <label for="eventStaff" class="form-label">参加スタッフ (複数選択可)</label>
                                <select class="form-select" id="eventStaff" name="staff[]" multiple
                                    aria-label="Select Staff" style="height: 150px;">
                                    @isset($users)
                                        @foreach ($users as $user)
                                            @if (Auth::id() !== $user->id) {{-- 自分自身を除外 --}}
                                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                                            @endif
                                        @endforeach
                                    @endisset
                                </select>
                                <small class="form-text text-muted">Ctrl (または Command) キーを押しながらクリックすると複数選択できます。</small>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">キャンセル</button>
                            <button type="submit" class="btn btn-primary">保存</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarElement = document.getElementById('calendar');
            var createEventModal = new bootstrap.Modal(document.getElementById(
                'createEventModal'));
            var eventForm = document.getElementById('createEventForm');
            var eventStartInput = document.getElementById('eventStart'); // 開始日時入力フィールド

            // カレンダー表示
            var calendar = new FullCalendar.Calendar(calendarElement, {
                initialView: 'dayGridMonth',
                locale: 'ja',
                events: '{{ route('api.events') }}',

                // FullCalendarドキュメント: dateClick
                // https://fullcalendar.io/docs/dateClick

            dateClick: function(info) {
                createEventModal.show();

                // クリックされた日付を開始日時フィールドに自動入力
                // info.dateStr は YYYY-MM-DD 形式の文字列
                // datetime-local には YYYY-MM-DDTHH:mm 形式が必要なので、T00:00 を追加
                eventStartInput.value = info.dateStr + 'T00:00';
            },

                // イベントクリック時の処理
            eventClick: function(info) {
                    // info.event.title でタイトル、info.event.start で開始日時などが取得できます
                    // 詳細表示モーダルなどをここに実装します
                    alert('Event: ' + info.event.title +
                        '\nStart: ' + info.event.start.toLocaleString() +
                        '\nEnd: ' + (info.event.end ? info.event.end.toLocaleString() : 'N/A') +
                        '\nCreator: ' + info.event.extendedProps.creator +
                        '\nStaff: ' + (Array.isArray(info.event.extendedProps.staff) ? info.event.extendedProps.staff.join(', ') : (info.event.extendedProps.staff || 'N/A'))
                    );
                },
            });
            calendar.render();

            // フォーム送信時の処理
            eventForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const userTimezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
                var formData = new FormData(eventForm);
                formData.append('user_timezone', userTimezone); // ユーザーのタイムゾーンを追加
                fetch('{{ route('events.store') }}', { // Ajaxでバックエンドに送信：　イベント作成APIのURL (POSTリクエスト)
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json' // JSONレスポンスを期待する
                        },
                        body: formData
                    })
                    .then(response => {
                        if (!response.ok) {
                            // HTTPステータスコードが200番台以外の場合のエラー処理
                            return response.json().then(data => {
                                throw new Error(data.message || 'イベントの保存に失敗しました');
                            });
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('イベント保存成功:', data);
                        createEventModal.hide();
                        eventForm.reset();

                        // カレンダーのイベントデータを再読み込みして表示を更新
                        calendar.refetchEvents();

                        // 成功メッセージ表示など
                        alert('イベントが保存されました！');
                    })
                    .catch(error => {
                        console.error('イベント保存エラー:', error);
                        alert('イベントの保存中にエラーが発生しました: ' + error.message);
                    });
            });
        });
    </script>
@endpush
