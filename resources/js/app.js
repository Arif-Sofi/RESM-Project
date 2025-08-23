import './bootstrap';
import './room-booking.js';
import './date-booking.js';
import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse'

Alpine.plugin(collapse)

window.Alpine = Alpine;

Alpine.start();
