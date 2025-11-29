<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\LocalizationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QrCodeController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Route::get('/set-locale/{locale}', [LocalizationController::class, 'setLocale'])->name('setLocale');
Route::get('/dashboard', function () {
    $bookings = auth()->user()->bookings()
        ->with('room')
        ->where('start_time', '>=', now())
        ->orderBy('start_time', 'asc')
        ->get();

    return view('dashboard', compact('bookings'));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Bookings routes
    Route::resource('bookings', controller: 'App\Http\Controllers\BookingController');
    Route::get('/my-bookings', [BookingController::class, 'myBookings'])->name('bookings.my-bookings');
    Route::get('/admin/approvals', [BookingController::class, 'approvals'])->name('admin.approvals');
    Route::get('/bookings/room/{room}', [BookingController::class, 'getBookingsByRoom']);
    Route::get('/bookings/room-and-date/{room}', [BookingController::class, 'getBookingsByRoomAndDate']);
    Route::post('/bookings/{booking}/approve', [BookingController::class, 'approve'])->name('bookings.approve');
    Route::post('/bookings/{booking}/reject', [BookingController::class, 'reject'])->name('bookings.reject');

    // API routes for bookings
    Route::get('/api/bookings', [BookingController::class, 'apiIndex'])->name('api.bookings');
    Route::post('/api/bookings/check-availability', [BookingController::class, 'checkAvailability'])->name('api.bookings.check-availability');
    Route::get('/api/bookings/available-rooms', [BookingController::class, 'availableRooms'])->name('api.bookings.available-rooms');
    Route::get('/api/bookings/{booking}', [BookingController::class, 'apiShow'])->name('api.bookings.show');

    // Rooms routes
    Route::resource('rooms', controller: 'App\Http\Controllers\RoomController');

    // QR Code routes
    Route::get('/qr-code', [QrCodeController::class, 'index'])->name('qr.index');
    Route::post('qr-code/generate', [QrCodeController::class, 'generate'])->name('qr.generate');
});

require __DIR__.'/auth.php';
