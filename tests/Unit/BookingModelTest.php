<?php

use App\Models\Booking;
use App\Models\Room;
use App\Models\User;
use Carbon\Carbon;

test('booking belongs to user relationship', function () {
    $user = User::factory()->create();
    $booking = Booking::factory()->create(['user_id' => $user->id]);

    expect($booking->user)->toBeInstanceOf(User::class)
        ->and($booking->user->id)->toBe($user->id);
});

test('booking belongs to room relationship', function () {
    $room = Room::factory()->create();
    $booking = Booking::factory()->create(['room_id' => $room->id]);

    expect($booking->room)->toBeInstanceOf(Room::class)
        ->and($booking->room->id)->toBe($room->id);
});

test('start_time attribute converts to Asia/Kuala_Lumpur timezone', function () {
    $utcTime = Carbon::now('UTC');
    $booking = Booking::factory()->create([
        'start_time' => $utcTime,
    ]);

    $booking->refresh();

    expect($booking->start_time->timezone->getName())->toBe('Asia/Kuala_Lumpur');
});

test('end_time attribute converts to Asia/Kuala_Lumpur timezone', function () {
    $utcTime = Carbon::now('UTC');
    $booking = Booking::factory()->create([
        'end_time' => $utcTime,
    ]);

    $booking->refresh();

    expect($booking->end_time->timezone->getName())->toBe('Asia/Kuala_Lumpur');
});

test('status is cast to boolean', function () {
    $booking = Booking::factory()->create(['status' => null]);
    expect($booking->status)->toBeNull();

    $booking = Booking::factory()->approved()->create();
    expect($booking->status)->toBeTrue();

    $booking = Booking::factory()->rejected()->create();
    expect($booking->status)->toBeFalse();
});
