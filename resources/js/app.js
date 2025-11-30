import './bootstrap';
import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse'
import $ from 'jquery';
import unifiedBookingComponent from './unified-booking.js';
import eventCalendarComponent from './event-calendar.js';

window.$ = $;
window.jQuery = $;

Alpine.plugin(collapse)

window.Alpine = Alpine;

// Register Alpine.js components
document.addEventListener('alpine:init', () => {
    Alpine.data('unifiedBooking', (rooms, authUserId) => unifiedBookingComponent(rooms, authUserId));
    Alpine.data('eventCalendar', (users, authUserId) => eventCalendarComponent(users, authUserId));
});

Alpine.start();
