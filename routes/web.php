<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Route::get('/set-locale/{locale}', [App\Http\Controllers\LocalizationController::class, 'setLocale'])->name('setLocale');
Route::get('/dashboard', function () {
    $bookings = auth()->user()->bookings()->get();
    return view('dashboard', compact('bookings'));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('events', controller: 'App\Http\Controllers\EventController');
    Route::delete('/events/{event}', [\App\Http\Controllers\EventController::class, 'destroy'])->name('events.destroy');
    Route::get('/api/events', [App\Http\Controllers\EventController::class, 'apiEvents'])->name('api.events');

    Route::resource('bookings', controller: 'App\Http\Controllers\BookingController');
    Route::get('/bookings/room/{room}', [App\Http\Controllers\BookingController::class, 'getBookingsByRoom']);
    Route::get('/bookings/room-and-date/{room}', [App\Http\Controllers\BookingController::class, 'getBookingsByRoomAndDate']);
    Route::patch('/bookings/{booking}/approve', [App\Http\Controllers\BookingController::class, 'approve'])->name('bookings.approve');
    Route::patch('/bookings/{booking}/reject', [App\Http\Controllers\BookingController::class, 'reject'])->name('bookings.reject');
    Route::resource('rooms', controller: 'App\Http\Controllers\RoomController');
});



require __DIR__.'/auth.php';
