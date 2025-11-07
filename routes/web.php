<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LocalizationController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\QrCodeController;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Route::get('/set-locale/{locale}', [LocalizationController::class, 'setLocale'])->name('setLocale');
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('events', controller: 'App\Http\Controllers\EventController');
    Route::delete('/events/{event}', [EventController::class, 'destroy'])->name('events.destroy');
    Route::get('/api/events', [EventController::class, 'apiEvents'])->name('api.events');

    Route::resource('bookings', controller: 'App\Http\Controllers\BookingController');
    Route::get('/my-bookings', [BookingController::class, 'myBookings'])->name('bookings.my');
    Route::get('/bookings/room/{room}', [BookingController::class, 'getBookingsByRoom']);
    Route::get('/bookings/room-and-date/{room}', [BookingController::class, 'getBookingsByRoomAndDate']);
    Route::patch('/bookings/{booking}/approve', [BookingController::class, 'approve'])->name('bookings.approve');
    Route::patch('/bookings/{booking}/reject', [BookingController::class, 'reject'])->name('bookings.reject');
    Route::resource('rooms', controller: 'App\Http\Controllers\RoomController');

    Route::get("/qr-code", [QrCodeController::class, "index"])->name("qr.index");
    Route::post("qr-code/generate", [QrCodeController::class, "generate"])->name("qr.generate");
});



require __DIR__.'/auth.php';
