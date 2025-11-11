<?php

use App\Models\Booking;
use App\Models\Room;
use App\Services\BookingService;
use Carbon\Carbon;

beforeEach(function () {
    $this->service = new BookingService();
    $this->room = Room::factory()->create();
});

test('isClash returns false when there are no existing bookings', function () {
    $startTime = Carbon::now()->addDay();
    $endTime = Carbon::now()->addDay()->addHours(2);

    expect($this->service->isClash($this->room->id, $startTime, $endTime))->toBeFalse();
});

test('isClash returns true when new booking completely overlaps existing booking', function () {
    $existingStart = Carbon::now()->addDay()->setHour(10);
    $existingEnd = Carbon::now()->addDay()->setHour(12);

    Booking::factory()->create([
        'room_id' => $this->room->id,
        'start_time' => $existingStart,
        'end_time' => $existingEnd,
    ]);

    $newStart = Carbon::now()->addDay()->setHour(9);
    $newEnd = Carbon::now()->addDay()->setHour(13);

    expect($this->service->isClash($this->room->id, $newStart, $newEnd))->toBeTrue();
});

test('isClash returns true when new booking is completely inside existing booking', function () {
    $existingStart = Carbon::now()->addDay()->setHour(9);
    $existingEnd = Carbon::now()->addDay()->setHour(13);

    Booking::factory()->create([
        'room_id' => $this->room->id,
        'start_time' => $existingStart,
        'end_time' => $existingEnd,
    ]);

    $newStart = Carbon::now()->addDay()->setHour(10);
    $newEnd = Carbon::now()->addDay()->setHour(12);

    expect($this->service->isClash($this->room->id, $newStart, $newEnd))->toBeTrue();
});

test('isClash returns true when new booking starts before and ends during existing booking', function () {
    $existingStart = Carbon::now()->addDay()->setHour(10);
    $existingEnd = Carbon::now()->addDay()->setHour(12);

    Booking::factory()->create([
        'room_id' => $this->room->id,
        'start_time' => $existingStart,
        'end_time' => $existingEnd,
    ]);

    $newStart = Carbon::now()->addDay()->setHour(9);
    $newEnd = Carbon::now()->addDay()->setHour(11);

    expect($this->service->isClash($this->room->id, $newStart, $newEnd))->toBeTrue();
});

test('isClash returns true when new booking starts during and ends after existing booking', function () {
    $existingStart = Carbon::now()->addDay()->setHour(10);
    $existingEnd = Carbon::now()->addDay()->setHour(12);

    Booking::factory()->create([
        'room_id' => $this->room->id,
        'start_time' => $existingStart,
        'end_time' => $existingEnd,
    ]);

    $newStart = Carbon::now()->addDay()->setHour(11);
    $newEnd = Carbon::now()->addDay()->setHour(13);

    expect($this->service->isClash($this->room->id, $newStart, $newEnd))->toBeTrue();
});

test('isClash returns false when new booking is before existing booking', function () {
    $existingStart = Carbon::now()->addDay()->setHour(10);
    $existingEnd = Carbon::now()->addDay()->setHour(12);

    Booking::factory()->create([
        'room_id' => $this->room->id,
        'start_time' => $existingStart,
        'end_time' => $existingEnd,
    ]);

    $newStart = Carbon::now()->addDay()->setHour(8);
    $newEnd = Carbon::now()->addDay()->setHour(9);

    expect($this->service->isClash($this->room->id, $newStart, $newEnd))->toBeFalse();
});

test('isClash returns false when new booking is after existing booking', function () {
    $existingStart = Carbon::now()->addDay()->setHour(10);
    $existingEnd = Carbon::now()->addDay()->setHour(12);

    Booking::factory()->create([
        'room_id' => $this->room->id,
        'start_time' => $existingStart,
        'end_time' => $existingEnd,
    ]);

    $newStart = Carbon::now()->addDay()->setHour(13);
    $newEnd = Carbon::now()->addDay()->setHour(15);

    expect($this->service->isClash($this->room->id, $newStart, $newEnd))->toBeFalse();
});

test('isClash returns false when new booking starts exactly when existing booking ends', function () {
    $existingStart = Carbon::now()->addDay()->setHour(10);
    $existingEnd = Carbon::now()->addDay()->setHour(12);

    Booking::factory()->create([
        'room_id' => $this->room->id,
        'start_time' => $existingStart,
        'end_time' => $existingEnd,
    ]);

    $newStart = Carbon::now()->addDay()->setHour(12);
    $newEnd = Carbon::now()->addDay()->setHour(14);

    expect($this->service->isClash($this->room->id, $newStart, $newEnd))->toBeFalse();
});

test('isClash returns false when new booking ends exactly when existing booking starts', function () {
    $existingStart = Carbon::now()->addDay()->setHour(10);
    $existingEnd = Carbon::now()->addDay()->setHour(12);

    Booking::factory()->create([
        'room_id' => $this->room->id,
        'start_time' => $existingStart,
        'end_time' => $existingEnd,
    ]);

    $newStart = Carbon::now()->addDay()->setHour(8);
    $newEnd = Carbon::now()->addDay()->setHour(10);

    expect($this->service->isClash($this->room->id, $newStart, $newEnd))->toBeFalse();
});

test('isClash only checks bookings for the specified room', function () {
    $otherRoom = Room::factory()->create();

    $existingStart = Carbon::now()->addDay()->setHour(10);
    $existingEnd = Carbon::now()->addDay()->setHour(12);

    Booking::factory()->create([
        'room_id' => $otherRoom->id,
        'start_time' => $existingStart,
        'end_time' => $existingEnd,
    ]);

    $newStart = Carbon::now()->addDay()->setHour(11);
    $newEnd = Carbon::now()->addDay()->setHour(13);

    expect($this->service->isClash($this->room->id, $newStart, $newEnd))->toBeFalse();
});

test('isClash handles multiple existing bookings correctly', function () {
    Booking::factory()->create([
        'room_id' => $this->room->id,
        'start_time' => Carbon::now()->addDay()->setHour(9),
        'end_time' => Carbon::now()->addDay()->setHour(10),
    ]);

    Booking::factory()->create([
        'room_id' => $this->room->id,
        'start_time' => Carbon::now()->addDay()->setHour(14),
        'end_time' => Carbon::now()->addDay()->setHour(16),
    ]);

    $newStart = Carbon::now()->addDay()->setHour(11);
    $newEnd = Carbon::now()->addDay()->setHour(13);

    expect($this->service->isClash($this->room->id, $newStart, $newEnd))->toBeFalse();
});

test('isClash detects clash with one of multiple existing bookings', function () {
    Booking::factory()->create([
        'room_id' => $this->room->id,
        'start_time' => Carbon::now()->addDay()->setHour(9),
        'end_time' => Carbon::now()->addDay()->setHour(10),
    ]);

    Booking::factory()->create([
        'room_id' => $this->room->id,
        'start_time' => Carbon::now()->addDay()->setHour(14),
        'end_time' => Carbon::now()->addDay()->setHour(16),
    ]);

    $newStart = Carbon::now()->addDay()->setHour(13);
    $newEnd = Carbon::now()->addDay()->setHour(15);

    expect($this->service->isClash($this->room->id, $newStart, $newEnd))->toBeTrue();
});
