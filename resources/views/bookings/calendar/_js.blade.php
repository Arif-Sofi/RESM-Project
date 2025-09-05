<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const previousBookings = @json($previousBookings ?? []);
        // FullCalendar用イベント配列に変換
        const events = previousBookings.map(b => ({
            id: b.id,
            title: b.purpose + (b.room_id ? ` (Room ${b.room_id})` : ''),
            start: b.start_time,
            end: b.end_time,
            backgroundColor: b.status === true ? '#22c55e' : (b.status === false ? '#ef4444' : '#facc15'),
            borderColor: '#888',
            extendedProps: {
                number_of_student: b.number_of_student,
                equipment_needed: b.equipment_needed,
                user_id: b.user_id
            }
        }));

        const calendarEl = document.getElementById('calendar');
        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            height: "auto",
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            events: events,
            // eventOverlap: false,
            // slotEventOverlap: false,
            eventClick: function(info) {
                const props = info.event.extendedProps;
                let details = `目的: ${info.event.title}\n`;
                details += `開始: ${info.event.start.toLocaleString()}\n終了: ${info.event.end ? info.event.end.toLocaleString() : ''}\n`;
                details += `人数: ${props.number_of_student}\n`;
                if(props.equipment_needed) details += `備品: ${props.equipment_needed}\n`;
                alert(details);
            }
        });
        calendar.render();
    });
</script>
