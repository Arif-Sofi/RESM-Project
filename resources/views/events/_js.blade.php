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

            // イベントクリック時の処理 (編集モーダルを開く)
            eventClick: function(info) {
                window.dispatchEvent(new CustomEvent('open-modal', {
                    detail: 'editEventModal'
                }));

                // Populate the edit form with event data
                const editEventId = document.getElementById('editEventId');
                const editEventTitle = document.getElementById('editEventTitle');
                const editEventDescription = document.getElementById('editEventDescription');
                const editEventStart = document.getElementById('editEventStart');
                const editEventStaffSelect = document.getElementById('editEventStaff');
                const editEventSubmitButton = document.querySelector(
                    '#editEventForm x-primary-button');

                editEventId.value = info.event.id;
                editEventTitle.value = info.event.title;
                editEventDescription.value = info.event.extendedProps.description || '';

                // Format dates for datetime-local input
                const start = info.event.start;
                const end = info.event.end;
                editEventStart.value = start ? start.toISOString().slice(0, 16) : '';
                // document.getElementById('editEventEnd').value = end ? end.toISOString().slice(0, 16) : ''; // Uncomment if using end date

                const staffIds = info.event.extendedProps.staff_ids || [];
                for (let i = 0; i < editEventStaffSelect.options.length; i++) {
                    editEventStaffSelect.options[i].selected = false;
                }

                // Select options based on staffIds
                for (let i = 0; i < editEventStaffSelect.options.length; i++) {
                    if (staffIds.includes(parseInt(editEventStaffSelect.options[i].value))) {
                        editEventStaffSelect.options[i].selected = true;
                    }
                }

                // 作成者限りの編集を許可
                const isCreator = '{{ Auth::id() }}' == info.event.extendedProps.creator_id;

                editEventTitle.disabled = !isCreator;
                editEventDescription.disabled = !isCreator;
                editEventStart.disabled = !isCreator;
                editEventStaffSelect.disabled = !isCreator;

                // 作成者限りの送信ボタンを有効化
                if (editEventSubmitButton) {
                    editEventSubmitButton.disabled = !isCreator;
                    editEventSubmitButton.classList.toggle('opacity-50 cursor-not-allowed', !
                        isCreator);
                }
            },
        });
        calendar.render();

        // フォームの送信ハンドラ
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
                    // console.log('イベント保存成功:', data);
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

        // 編集フォームの送信ハンドラ
        const editEventForm = document.getElementById('editEventForm');
        const deleteEventButton = document.getElementById('deleteEventButton');

        if (editEventForm) {
            editEventForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const eventId = document.getElementById('editEventId').value;
                const userTimezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
                var formData = new FormData(editEventForm);
                formData.append('_method', 'PATCH');
                formData.append('user_timezone', userTimezone);

                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute(
                    'content') || editEventForm.querySelector('input[name="_token"]')?.value;

                fetch(`/events/${eventId}`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: formData
                    })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(data => {
                                const errors = data.errors ? Object.values(data.errors)
                                    .flat().join('\n') : (data.message || 'イベントの更新に失敗しました');
                                throw new Error(errors);
                            });
                        }
                        return response.json();
                    })
                    .then(data => {
                        window.dispatchEvent(new CustomEvent('close'));
                        if (window.calendar) {
                            calendar.refetchEvents();
                        }
                        alert('イベントが更新されました！');
                    })
                    .catch(error => {
                        console.error('イベント更新エラー:', error);
                        alert('イベントの更新中にエラーが発生しました:\n' + error.message);
                    });
            });
        }

        // 削除ボタンのハンドラ
        if (deleteEventButton) {
            deleteEventButton.addEventListener('click', function() {
                if (confirm('本当にこのイベントを削除しますか？')) {
                    const eventId = document.getElementById('editEventId')
                        .value;
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute(
                            'content') || document.querySelector('#editEventForm input[name="_token"]')
                        ?.value;

                    fetch(`/events/${eventId}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json',
                                'Content-Type': 'application/json'
                            },
                        })
                        .then(response => {
                            if (!response.ok) {
                                return response.json().then(data => {
                                    throw new Error(data.message || 'イベントの削除に失敗しました');
                                });
                            }
                            const contentType = response.headers.get("content-type");
                            if (contentType && contentType.indexOf("application/json") !== -1) {
                                return response.json();
                            } else {
                                return {};
                            }
                        })
                        .then(data => {
                            window.dispatchEvent(new CustomEvent('close'));
                            if (window.calendar) {
                                calendar.refetchEvents();
                            }
                            alert('イベントが削除されました！');
                        })
                        .catch(error => {
                            console.error('イベント削除エラー:', error);
                            alert('イベントの削除中にエラーが発生しました: ' + error.message);
                        });
                }
            });
        }

        // 全スタッフ選択ボタンのハンドラ (Create Modal)
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

        // 全スタッフ選択ボタンのハンドラ (Edit Modal)
        const selectAllStaffButtonEdit = document.getElementById('selectAllStaffButtonEdit');
        if (selectAllStaffButtonEdit) {
            selectAllStaffButtonEdit.addEventListener('click', function() {
                const editEventStaffSelect = document.getElementById('editEventStaff');
                if (editEventStaffSelect) {
                    for (let i = 0; i < editEventStaffSelect.options.length; i++) {
                        editEventStaffSelect.options[i].selected = true;
                    }
                }
            });
        }

        // 全スタッフの選択を解除ボタンのハンドラ (Create Modal)
        const cancelAllStaffButton = document.getElementById('cancelAllStaffButton');
        if (cancelAllStaffButton) {
            cancelAllStaffButton.addEventListener('click', function() {
                const eventStaffSelect = document.getElementById('eventStaff');
                if (eventStaffSelect) {
                    for (let i = 0; i < eventStaffSelect.options.length; i++) {
                        eventStaffSelect.options[i].selected = false;
                    }
                }
            });
        }

        // 全スタッフの選択を解除ボタンのハンドラ (Edit Modal)
        const cancelAllStaffButtonEdit = document.getElementById('cancelAllStaffButtonEdit');
        if (cancelAllStaffButtonEdit) {
            cancelAllStaffButtonEdit.addEventListener('click', function() {
                const editEventStaffSelect = document.getElementById('editEventStaff');
                if (editEventStaffSelect) {
                    for (let i = 0; i < editEventStaffSelect.options.length; i++) {
                        editEventStaffSelect.options[i].selected = false;
                    }
                }
            });
        }
    });
</script>
