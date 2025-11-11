<?php

use App\Models\Booking;
use App\Models\Room;

test('room has many bookings relationship', function () {
    $room = Room::factory()->create();
    $booking1 = Booking::factory()->create(['room_id' => $room->id]);
    $booking2 = Booking::factory()->create(['room_id' => $room->id]);

    expect($room->bookings)->toHaveCount(2)
        ->and($room->bookings->pluck('id')->toArray())->toContain($booking1->id, $booking2->id);
});

test('room name must be unique', function () {
    $room = Room::factory()->create(['name' => 'Unique Room']);

    expect(fn () => Room::factory()->create(['name' => 'Unique Room']))
        ->toThrow(Illuminate\Database\QueryException::class);
});
