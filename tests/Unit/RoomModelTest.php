<?php

use App\Models\Booking;
use App\Models\Room;
use Illuminate\Database\QueryException;

describe('Room Model Relationships', function () {
    test('room has many bookings relationship', function () {
        $room = Room::factory()->create();
        $booking1 = Booking::factory()->create(['room_id' => $room->id]);
        $booking2 = Booking::factory()->create(['room_id' => $room->id]);

        expect($room->bookings)->toHaveCount(2)
            ->and($room->bookings->pluck('id')->toArray())->toContain($booking1->id, $booking2->id);
    });

    test('room can have zero bookings', function () {
        $room = Room::factory()->create();

        expect($room->bookings)->toHaveCount(0)
            ->and($room->bookings)->toBeInstanceOf(\Illuminate\Database\Eloquent\Collection::class);
    });

    test('room can have many bookings from different users', function () {
        $room = Room::factory()->create();
        $booking1 = Booking::factory()->create(['room_id' => $room->id]);
        $booking2 = Booking::factory()->create(['room_id' => $room->id]);
        $booking3 = Booking::factory()->create(['room_id' => $room->id]);

        expect($room->bookings()->count())->toBe(3);
    });

    test('deleting room does not cascade delete bookings automatically', function () {
        $room = Room::factory()->create();
        $booking = Booking::factory()->create(['room_id' => $room->id]);

        // Foreign key constraint should prevent deletion
        expect(fn () => $room->delete())->toThrow(QueryException::class);
    });
});

describe('Room Model Validation and Constraints', function () {
    test('room name must be unique', function () {
        Room::factory()->create(['name' => 'Unique Room']);

        expect(fn () => Room::factory()->create(['name' => 'Unique Room']))
            ->toThrow(QueryException::class);
    });

    test('room name is case-sensitive for uniqueness', function () {
        Room::factory()->create(['name' => 'Conference Room A']);

        // Different case should be allowed (depending on database collation)
        $room = Room::factory()->create(['name' => 'CONFERENCE ROOM A']);

        expect($room)->toBeInstanceOf(Room::class);
    });

    test('multiple rooms can have different names', function () {
        $room1 = Room::factory()->create(['name' => 'Room A']);
        $room2 = Room::factory()->create(['name' => 'Room B']);
        $room3 = Room::factory()->create(['name' => 'Room C']);

        expect(Room::count())->toBeGreaterThanOrEqual(3);
    });
});

describe('Room Model Fillable Attributes', function () {
    test('fillable attributes can be mass assigned', function () {
        $room = Room::create([
            'name' => 'Test Room',
            'description' => 'A room for testing',
            'location_details' => 'Building A, Floor 3',
        ]);

        expect($room)->toBeInstanceOf(Room::class)
            ->and($room->name)->toBe('Test Room')
            ->and($room->description)->toBe('A room for testing')
            ->and($room->location_details)->toBe('Building A, Floor 3');
    });

    test('description can be null', function () {
        $room = Room::factory()->create(['description' => null]);

        expect($room->description)->toBeNull();
    });

    test('location_details can be null', function () {
        $room = Room::factory()->create(['location_details' => null]);

        expect($room->location_details)->toBeNull();
    });

    test('room can be updated with new attributes', function () {
        $room = Room::factory()->create([
            'name' => 'Old Name',
            'description' => 'Old description',
        ]);

        $room->update([
            'name' => 'New Name',
            'description' => 'New description',
            'location_details' => 'New location',
        ]);

        expect($room->fresh()->name)->toBe('New Name')
            ->and($room->fresh()->description)->toBe('New description')
            ->and($room->fresh()->location_details)->toBe('New location');
    });
});

describe('Room Model Factory', function () {
    test('factory creates valid room', function () {
        $room = Room::factory()->create();

        expect($room)->toBeInstanceOf(Room::class)
            ->and($room->name)->not->toBeNull()
            ->and($room->id)->not->toBeNull();
    });

    test('factory can create multiple rooms with unique names', function () {
        $rooms = Room::factory()->count(5)->create();

        $names = $rooms->pluck('name')->toArray();
        $uniqueNames = array_unique($names);

        expect(count($names))->toBe(count($uniqueNames));
    });
});

describe('Room Model Timestamps', function () {
    test('timestamps are automatically managed', function () {
        $room = Room::factory()->create();

        expect($room->created_at)->not->toBeNull()
            ->and($room->updated_at)->not->toBeNull()
            ->and($room->created_at)->toBeInstanceOf(\Carbon\Carbon::class)
            ->and($room->updated_at)->toBeInstanceOf(\Carbon\Carbon::class);
    });

    test('updated_at changes when room is updated', function () {
        $room = Room::factory()->create();
        $originalUpdatedAt = $room->updated_at;

        sleep(1);
        $room->update(['name' => 'Updated Name']);

        expect($room->fresh()->updated_at->greaterThan($originalUpdatedAt))->toBeTrue();
    });
});

describe('Room Model Edge Cases', function () {
    test('room handles very long name', function () {
        $longName = str_repeat('Conference Room ', 10);
        $room = Room::factory()->create(['name' => $longName]);

        expect($room->name)->toBe($longName);
    });

    test('room handles very long description', function () {
        $longDescription = str_repeat('This is a description. ', 50);
        $room = Room::factory()->create(['description' => $longDescription]);

        expect($room->description)->toBe($longDescription);
    });

    test('room handles special characters in name', function () {
        $room = Room::factory()->create(['name' => 'Room & Conference @ Hall #1']);

        expect($room->name)->toBe('Room & Conference @ Hall #1');
    });

    test('room can be queried by name', function () {
        $room = Room::factory()->create(['name' => 'Searchable Room']);

        $found = Room::where('name', 'Searchable Room')->first();

        expect($found->id)->toBe($room->id);
    });
});
