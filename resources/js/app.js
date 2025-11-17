import './bootstrap';
import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse'
import $ from 'jquery';
import unifiedBookingComponent from './unified-booking.js';

window.$ = $;
window.jQuery = $;

Alpine.plugin(collapse)

window.Alpine = Alpine;

// Register the unified booking component
document.addEventListener('alpine:init', () => {
    Alpine.data('unifiedBooking', (rooms, authUserId) => unifiedBookingComponent(rooms, authUserId));
});

Alpine.start();
