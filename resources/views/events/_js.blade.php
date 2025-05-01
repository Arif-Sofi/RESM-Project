<script>
    document.addEventListener('DOMContentLoaded', function() {
        var calendarElement = document.getElementById('calendar');
        var eventForm = document.getElementById('createEventForm');
        var eventStartInput = document.getElementById('eventStart'); // 開始日時入力フィールド

        // カレンダー表示
        var calendar = new FullCalendar.Calendar(calendarElement, {
            initialView: 'dayGridMonth',
            locale: 'ja',
            events: '{{ route('api.events') }}',
            height: '100%',
            width: '100%',

            // クリック時の処理
            dateClick: function(info) {
                window.dispatchEvent(new CustomEvent('open-modal', {
                    detail: 'createEventModal'
                }));
                // クリックされた日付を開始日時フィールドに自動入力
                eventStartInput.value = info.dateStr + 'T00:00';
            },

            // イベントクリック時の処理
            eventClick: function(info) {
                alert('Event: ' + info.event.title +
                    '\nStart: ' + info.event.start.toLocaleString() +
                    '\nEnd: ' + (info.event.end ? info.event.end.toLocaleString() : 'N/A') +
                    '\nCreator: ' + info.event.extendedProps.creator +
                    '\nStaff: ' + (Array.isArray(info.event.extendedProps.staff) ? info.event
                        .extendedProps.staff.join(', ') : (info.event.extendedProps.staff ||
                            'N/A'))
                );
            },
        });
        calendar.render();

        // フォーム送信時の処理
        eventForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const userTimezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
            var formData = new FormData(eventForm);
            formData.append('user_timezone', userTimezone);
            fetch('{{ route('events.store') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: formData
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(data => {
                            throw new Error(data.message || 'イベントの保存に失敗しました');
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('イベント保存成功:', data);
                    window.dispatchEvent(new CustomEvent('close'));
                    eventForm.reset();
                    calendar.refetchEvents();
                    alert('イベントが保存されました！');
                })
                .catch(error => {
                    console.error('イベント保存エラー:', error);
                    alert('イベントの保存中にエラーが発生しました: ' + error.message);
                });
        });
    });

    // 全スタッフ選択ボタンの処理
    document.addEventListener('DOMContentLoaded', function() {
        const selectAllStaffButton = document.getElementById('selectAllStaffButton');
        if (selectAllStaffButton) {
            selectAllStaffButton.addEventListener('click', function() {
                const eventStaffSelect = document.getElementById('eventStaff');
                if (eventStaffSelect) {
                    for (let i = 0; i < eventStaffSelect.options.length; i++) {
                        eventStaffSelect.options[i].selected = true;
                    }
                }
            });
        }
    });
</script>
