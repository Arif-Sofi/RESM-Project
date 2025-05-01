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
                // Open the edit modal
                window.dispatchEvent(new CustomEvent('open-modal', {
                    detail: 'editEventModal'
                }));

                // Populate the edit form with event data
                const editEventId = document.getElementById('editEventId');
                const editEventTitle = document.getElementById('editEventTitle');
                const editEventDescription = document.getElementById('editEventDescription');
                const editEventStart = document.getElementById('editEventStart');
                const editEventStaffSelect = document.getElementById('editEventStaff');
                const editEventSubmitButton = document.querySelector('#editEventForm x-primary-button'); // Assuming x-primary-button is the submit button

                editEventId.value = info.event.id;
                editEventTitle.value = info.event.title;
                editEventDescription.value = info.event.extendedProps.description || '';

                // Format dates for datetime-local input
                const start = info.event.start;
                const end = info.event.end;
                editEventStart.value = start ? start.toISOString().slice(0, 16) : '';
                // document.getElementById('editEventEnd').value = end ? end.toISOString().slice(0, 16) : ''; // Uncomment if using end date

                // Select staff
                const staffIds = info.event.extendedProps.staff_ids || []; // Assuming staff_ids is available in extendedProps

                // Deselect all options first
                for (let i = 0; i < editEventStaffSelect.options.length; i++) {
                    editEventStaffSelect.options[i].selected = false;
                }

                // Select options based on staffIds
                for (let i = 0; i < editEventStaffSelect.options.length; i++) {
                    if (staffIds.includes(parseInt(editEventStaffSelect.options[i].value))) {
                        editEventStaffSelect.options[i].selected = true;
                    }
                }

                // Disable form elements if the user is not the creator
                const isCreator = '{{ Auth::id() }}' == info.event.extendedProps.creator_id;

                editEventTitle.disabled = !isCreator;
                editEventDescription.disabled = !isCreator;
                editEventStart.disabled = !isCreator;
                editEventStaffSelect.disabled = !isCreator;

                // Disable submit button if not the creator
                if (editEventSubmitButton) {
                    editEventSubmitButton.disabled = !isCreator;
                    editEventSubmitButton.classList.toggle('opacity-50 cursor-not-allowed', !isCreator); // Optional: visually indicate disabled state
                }
            },
        });
        calendar.render();

        // Create form submission handler
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

        // Edit form submission handler
        const editEventForm = document.getElementById('editEventForm');
        if (editEventForm) {
            editEventForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const eventId = document.getElementById('editEventId').value;
                const userTimezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
                var formData = new FormData(editEventForm);
                formData.append('_method', 'PATCH'); // Use PATCH method for update
                formData.append('user_timezone', userTimezone);

                fetch(`/events/${eventId}`, { // Assuming the update route is /events/{id}
                        method: 'POST', // Fetch requires POST for PATCH/PUT with FormData
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: formData
                    })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(data => {
                                throw new Error(data.message || 'イベントの更新に失敗しました');
                            });
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('イベント更新成功:', data);
                        window.dispatchEvent(new CustomEvent('close'));
                        calendar.refetchEvents(); // Refresh calendar events
                        alert('イベントが更新されました！');
                    })
                    .catch(error => {
                        console.error('イベント更新エラー:', error);
                        alert('イベントの更新中にエラーが発生しました: ' + error.message);
                    });
            });
        }
    });

    // Select All Staff button handler for Create Modal
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

    // Select All Staff button handler for Edit Modal
    document.addEventListener('DOMContentLoaded', function() {
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
    });
</script>
