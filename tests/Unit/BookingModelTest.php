<?php

use App\Models\Booking;
use App\Models\Room;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\QueryException;

describe('Booking Model Relationships', function () {
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

    test('user can have multiple bookings', function () {
        $user = User::factory()->create();
        $bookings = Booking::factory()->count(3)->create(['user_id' => $user->id]);

        expect($user->bookings()->count())->toBe(3)
            ->and($user->bookings->first())->toBeInstanceOf(Booking::class);
    });

    test('room can have multiple bookings', function () {
        $room = Room::factory()->create();
        $bookings = Booking::factory()->count(3)->create(['room_id' => $room->id]);

        expect($room->bookings()->count())->toBe(3)
            ->and($room->bookings->first())->toBeInstanceOf(Booking::class);
    });
});

describe('Booking Model Timezone Handling', function () {
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

    test('start_time maintains correct datetime when converting timezone', function () {
        $utcTime = Carbon::create(2025, 1, 15, 10, 0, 0, 'UTC'); // 10:00 UTC
        $booking = Booking::factory()->create([
            'start_time' => $utcTime,
        ]);

        $booking->refresh();

        // 10:00 UTC = 18:00 Asia/Kuala_Lumpur (UTC+8)
        expect($booking->start_time->hour)->toBe(18)
            ->and($booking->start_time->format('Y-m-d'))->toBe('2025-01-15');
    });

    test('timezone conversion handles edge cases across date boundaries', function () {
        $utcTime = Carbon::create(2025, 1, 15, 23, 0, 0, 'UTC'); // 23:00 UTC
        $booking = Booking::factory()->create([
            'start_time' => $utcTime,
        ]);

        $booking->refresh();

        // 23:00 UTC = 07:00 next day Asia/Kuala_Lumpur (UTC+8)
        expect($booking->start_time->hour)->toBe(7)
            ->and($booking->start_time->format('Y-m-d'))->toBe('2025-01-16');
    });
});

describe('Booking Model Status Casting', function () {
    test('status is cast to boolean when approved', function () {
        $booking = Booking::factory()->approved()->create();

        expect($booking->status)->toBeTrue()
            ->and($booking->status)->toBeBool();
    });

    test('status is cast to boolean when rejected', function () {
        $booking = Booking::factory()->rejected()->create();

        expect($booking->status)->toBeFalse()
            ->and($booking->status)->toBeBool();
    });

    test('status is null when pending', function () {
        $booking = Booking::factory()->pending()->create();

        expect($booking->status)->toBeNull();
    });

    test('rejection_reason is set only when rejected', function () {
        $rejectedBooking = Booking::factory()->rejected()->create();
        $approvedBooking = Booking::factory()->approved()->create();

        expect($rejectedBooking->rejection_reason)->not->toBeNull()
            ->and($approvedBooking->rejection_reason)->toBeNull();
    });
});

describe('Booking Model Fillable Attributes', function () {
    test('fillable attributes can be mass assigned', function () {
        $user = User::factory()->create();
        $room = Room::factory()->create();

        $booking = Booking::create([
            'room_id' => $room->id,
            'user_id' => $user->id,
            'start_time' => now()->addDay(),
            'end_time' => now()->addDay()->addHours(2),
            'status' => null,
            'number_of_student' => 25,
            'equipment_needed' => 'Projector, Whiteboard',
            'purpose' => 'Team meeting',
            'rejection_reason' => null,
        ]);

        expect($booking)->toBeInstanceOf(Booking::class)
            ->and($booking->room_id)->toBe($room->id)
            ->and($booking->user_id)->toBe($user->id)
            ->and($booking->number_of_student)->toBe(25)
            ->and($booking->equipment_needed)->toBe('Projector, Whiteboard')
            ->and($booking->purpose)->toBe('Team meeting');
    });

    test('timestamps are automatically managed', function () {
        $booking = Booking::factory()->create();

        expect($booking->created_at)->not->toBeNull()
            ->and($booking->updated_at)->not->toBeNull()
            ->and($booking->created_at)->toBeInstanceOf(Carbon::class)
            ->and($booking->updated_at)->toBeInstanceOf(Carbon::class);
    });
});

describe('Booking Model Database Constraints', function () {
    test('booking requires room_id foreign key', function () {
        expect(fn() => Booking::factory()->create(['room_id' => 99999]))
            ->toThrow(QueryException::class);
    });

    test('booking requires user_id foreign key', function () {
        expect(fn() => Booking::factory()->create(['user_id' => 99999]))
            ->toThrow(QueryException::class);
    });
});

describe('Booking Model Factory States', function () {
    test('factory creates pending booking by default', function () {
        $booking = Booking::factory()->create();

        expect($booking->status)->toBeNull()
            ->and($booking->rejection_reason)->toBeNull();
    });

    test('factory can create approved booking', function () {
        $booking = Booking::factory()->approved()->create();

        expect($booking->status)->toBeTrue()
            ->and($booking->rejection_reason)->toBeNull();
    });

    test('factory can create rejected booking', function () {
        $booking = Booking::factory()->rejected()->create();

        expect($booking->status)->toBeFalse()
            ->and($booking->rejection_reason)->not->toBeNull();
    });

    test('factory generates valid time ranges', function () {
        $booking = Booking::factory()->create();

        expect($booking->end_time->greaterThan($booking->start_time))->toBeTrue()
            ->and($booking->start_time->isFuture())->toBeTrue();
    });

    test('factory generates realistic student numbers', function () {
        $bookings = Booking::factory()->count(10)->create();

        foreach ($bookings as $booking) {
            expect($booking->number_of_student)->toBeGreaterThan(0)
                ->and($booking->number_of_student)->toBeLessThanOrEqual(50);
        }
    });
});

describe('Booking Model Edge Cases', function () {
    test('booking handles very long purpose text', function () {
        $longPurpose = str_repeat('This is a very long purpose description. ', 50);
        $booking = Booking::factory()->create(['purpose' => $longPurpose]);

        expect($booking->purpose)->toBe($longPurpose);
    });

    test('booking handles optional equipment_needed as null', function () {
        $booking = Booking::factory()->create(['equipment_needed' => null]);

        expect($booking->equipment_needed)->toBeNull();
    });

    test('booking can be updated with new dates', function () {
        $booking = Booking::factory()->create();
        $newStartTime = now()->addDays(10);
        $newEndTime = now()->addDays(10)->addHours(3);

        $booking->update([
            'start_time' => $newStartTime,
            'end_time' => $newEndTime,
        ]);

        $booking->refresh();

        expect($booking->start_time->timezone->getName())->toBe('Asia/Kuala_Lumpur')
            ->and($booking->end_time->timezone->getName())->toBe('Asia/Kuala_Lumpur');
    });

    test('status can transition from pending to approved', function () {
        $booking = Booking::factory()->pending()->create();
        expect($booking->status)->toBeNull();

        $booking->update(['status' => true]);
        expect($booking->status)->toBeTrue();
    });

    test('status can transition from pending to rejected with reason', function () {
        $booking = Booking::factory()->pending()->create();
        expect($booking->status)->toBeNull();

        $booking->update([
            'status' => false,
            'rejection_reason' => 'Room not available',
        ]);

        expect($booking->status)->toBeFalse()
            ->and($booking->rejection_reason)->toBe('Room not available');
    });
});
