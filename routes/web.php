<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\LocalizationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QrCodeController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Route::get('/set-locale/{locale}', [LocalizationController::class, 'setLocale'])->name('setLocale');
Route::get('/dashboard', function () {
    $bookings = auth()->user()->bookings()
        ->with('room')
        ->where('start_time', '>=', now())
        ->orderBy('start_time', 'asc')
        ->get();
    $rooms = \App\Models\Room::all();

    return view('dashboard', compact('bookings', 'rooms'));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // Reports & Export (Must be before resource routes to avoid ID conflict)
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/bookings/export', [BookingController::class, 'export'])->name('bookings.export');
    Route::get('/events/export', [EventController::class, 'export'])->name('events.export');

    // Booking Routes
    Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
    Route::get('/bookings/create', [BookingController::class, 'create'])->name('bookings.create');
    Route::get('/bookings/{booking}/edit', [BookingController::class, 'edit'])->name('bookings.edit');
    Route::patch('/bookings/{booking}', [BookingController::class, 'update'])->name('bookings.update');
    Route::delete('/bookings/{booking}', [BookingController::class, 'destroy'])->name('bookings.destroy');
    Route::get('/bookings/my-bookings', [BookingController::class, 'myBookings'])->name('bookings.my-bookings');

    // API Routes for Booking (Calendar)
    Route::get('/api/bookings', [BookingController::class, 'apiIndex'])->name('api.bookings.index');
    Route::get('/api/bookings/{booking}', [BookingController::class, 'apiShow'])->name('api.bookings.show');
    Route::post('/api/bookings/check-availability', [BookingController::class, 'checkAvailability'])->name('api.bookings.check-availability');
    Route::get('/api/bookings/available-rooms', [BookingController::class, 'availableRooms'])->name('api.bookings.available-rooms');

    // Booking AJAX helpers
    Route::get('/bookings/room/{room}', [BookingController::class, 'getBookingsByRoom'])->name('bookings.by-room');
    Route::get('/bookings/room-and-date/{room}', [BookingController::class, 'getBookingsByRoomAndDate'])->name('bookings.by-room-and-date');

    // Admin Booking Approval
    Route::get('/admin/approvals', [BookingController::class, 'approvals'])->name('admin.approvals');
    Route::post('/bookings/{booking}/approve', [BookingController::class, 'approve'])->name('bookings.approve');
    Route::post('/bookings/{booking}/reject', [BookingController::class, 'reject'])->name('bookings.reject');

    // Event Routes
    Route::resource('events', EventController::class);
    Route::get('/my-events', [EventController::class, 'myEvents'])->name('events.my-events');
    Route::post('/events/import', [EventController::class, 'import'])->name('events.import');
    Route::get('/api/events', [EventController::class, 'apiEvents'])->name('api.events');

    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // QR Code
    Route::get('/qr-scanner', [QrCodeController::class, 'index'])->name('qr.scanner');
    Route::post('/qr/check-in', [QrCodeController::class, 'checkIn'])->name('qr.check-in');

    // Notifications
    Route::post('/notifications/{id}/read', function ($id) {
        auth()->user()->unreadNotifications->where('id', $id)->markAsRead();

        return response()->json(['success' => true]);
    })->name('notifications.read');

    Route::post('/notifications/read-all', function () {
        auth()->user()->unreadNotifications->markAsRead();

        return response()->json(['success' => true]);
    })->name('notifications.read-all');
});

require __DIR__.'/auth.php';
