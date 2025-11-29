<?php

use App\Models\Booking;
use App\Models\Room;
use App\Services\BookingService;
use Carbon\Carbon;

beforeEach(function () {
    $this->service = new BookingService;
    $this->room = Room::factory()->create();
});

describe('BookingService Basic Clash Detection', function () {
    test('isClash returns false when there are no existing bookings', function () {
        $startTime = Carbon::now()->addDay();
        $endTime = Carbon::now()->addDay()->addHours(2);

        expect($this->service->isClash($this->room->id, $startTime, $endTime))->toBeFalse();
    });

    test('isClash returns true when new booking completely overlaps existing booking', function () {
        $existingStart = Carbon::now()->addDay()->setHour(10)->setMinute(0);
        $existingEnd = Carbon::now()->addDay()->setHour(12)->setMinute(0);

        Booking::factory()->create([
            'room_id' => $this->room->id,
            'start_time' => $existingStart,
            'end_time' => $existingEnd,
        ]);

        $newStart = Carbon::now()->addDay()->setHour(9)->setMinute(0);
        $newEnd = Carbon::now()->addDay()->setHour(13)->setMinute(0);

        expect($this->service->isClash($this->room->id, $newStart, $newEnd))->toBeTrue();
    });

    test('isClash returns true when new booking is completely inside existing booking', function () {
        $existingStart = Carbon::now()->addDay()->setHour(9)->setMinute(0);
        $existingEnd = Carbon::now()->addDay()->setHour(13)->setMinute(0);

        Booking::factory()->create([
            'room_id' => $this->room->id,
            'start_time' => $existingStart,
            'end_time' => $existingEnd,
        ]);

        $newStart = Carbon::now()->addDay()->setHour(10)->setMinute(0);
        $newEnd = Carbon::now()->addDay()->setHour(12)->setMinute(0);

        expect($this->service->isClash($this->room->id, $newStart, $newEnd))->toBeTrue();
    });

    test('isClash returns true when new booking starts before and ends during existing booking', function () {
        $existingStart = Carbon::now()->addDay()->setHour(10)->setMinute(0);
        $existingEnd = Carbon::now()->addDay()->setHour(12)->setMinute(0);

        Booking::factory()->create([
            'room_id' => $this->room->id,
            'start_time' => $existingStart,
            'end_time' => $existingEnd,
        ]);

        $newStart = Carbon::now()->addDay()->setHour(9)->setMinute(0);
        $newEnd = Carbon::now()->addDay()->setHour(11)->setMinute(0);

        expect($this->service->isClash($this->room->id, $newStart, $newEnd))->toBeTrue();
    });

    test('isClash returns true when new booking starts during and ends after existing booking', function () {
        $existingStart = Carbon::now()->addDay()->setHour(10)->setMinute(0);
        $existingEnd = Carbon::now()->addDay()->setHour(12)->setMinute(0);

        Booking::factory()->create([
            'room_id' => $this->room->id,
            'start_time' => $existingStart,
            'end_time' => $existingEnd,
        ]);

        $newStart = Carbon::now()->addDay()->setHour(11)->setMinute(0);
        $newEnd = Carbon::now()->addDay()->setHour(13)->setMinute(0);

        expect($this->service->isClash($this->room->id, $newStart, $newEnd))->toBeTrue();
    });

    test('isClash returns false when new booking is completely before existing booking', function () {
        $existingStart = Carbon::now()->addDay()->setHour(10)->setMinute(0);
        $existingEnd = Carbon::now()->addDay()->setHour(12)->setMinute(0);

        Booking::factory()->create([
            'room_id' => $this->room->id,
            'start_time' => $existingStart,
            'end_time' => $existingEnd,
        ]);

        $newStart = Carbon::now()->addDay()->setHour(8)->setMinute(0);
        $newEnd = Carbon::now()->addDay()->setHour(9)->setMinute(0);

        expect($this->service->isClash($this->room->id, $newStart, $newEnd))->toBeFalse();
    });

    test('isClash returns false when new booking is completely after existing booking', function () {
        $existingStart = Carbon::now()->addDay()->setHour(10)->setMinute(0);
        $existingEnd = Carbon::now()->addDay()->setHour(12)->setMinute(0);

        Booking::factory()->create([
            'room_id' => $this->room->id,
            'start_time' => $existingStart,
            'end_time' => $existingEnd,
        ]);

        $newStart = Carbon::now()->addDay()->setHour(13)->setMinute(0);
        $newEnd = Carbon::now()->addDay()->setHour(15)->setMinute(0);

        expect($this->service->isClash($this->room->id, $newStart, $newEnd))->toBeFalse();
    });
});

describe('BookingService Boundary Condition Tests', function () {
    test('isClash returns false when new booking starts exactly when existing booking ends', function () {
        $existingStart = Carbon::now()->addDay()->setHour(10)->setMinute(0)->setSecond(0);
        $existingEnd = Carbon::now()->addDay()->setHour(12)->setMinute(0)->setSecond(0);

        Booking::factory()->create([
            'room_id' => $this->room->id,
            'start_time' => $existingStart,
            'end_time' => $existingEnd,
        ]);

        $newStart = Carbon::now()->addDay()->setHour(12)->setMinute(0)->setSecond(0);
        $newEnd = Carbon::now()->addDay()->setHour(14)->setMinute(0)->setSecond(0);

        expect($this->service->isClash($this->room->id, $newStart, $newEnd))->toBeFalse();
    });

    test('isClash returns false when new booking ends exactly when existing booking starts', function () {
        $existingStart = Carbon::now()->addDay()->setHour(10)->setMinute(0)->setSecond(0);
        $existingEnd = Carbon::now()->addDay()->setHour(12)->setMinute(0)->setSecond(0);

        Booking::factory()->create([
            'room_id' => $this->room->id,
            'start_time' => $existingStart,
            'end_time' => $existingEnd,
        ]);

        $newStart = Carbon::now()->addDay()->setHour(8)->setMinute(0)->setSecond(0);
        $newEnd = Carbon::now()->addDay()->setHour(10)->setMinute(0)->setSecond(0);

        expect($this->service->isClash($this->room->id, $newStart, $newEnd))->toBeFalse();
    });

    test('isClash returns true when new booking overlaps by one second at start', function () {
        $existingStart = Carbon::now()->addDay()->setHour(10)->setMinute(0)->setSecond(0);
        $existingEnd = Carbon::now()->addDay()->setHour(12)->setMinute(0)->setSecond(0);

        Booking::factory()->create([
            'room_id' => $this->room->id,
            'start_time' => $existingStart,
            'end_time' => $existingEnd,
        ]);

        $newStart = Carbon::now()->addDay()->setHour(9)->setMinute(59)->setSecond(59);
        $newEnd = Carbon::now()->addDay()->setHour(11)->setMinute(0)->setSecond(0);

        expect($this->service->isClash($this->room->id, $newStart, $newEnd))->toBeTrue();
    });

    test('isClash returns true when new booking overlaps by one second at end', function () {
        $existingStart = Carbon::now()->addDay()->setHour(10)->setMinute(0)->setSecond(0);
        $existingEnd = Carbon::now()->addDay()->setHour(12)->setMinute(0)->setSecond(0);

        Booking::factory()->create([
            'room_id' => $this->room->id,
            'start_time' => $existingStart,
            'end_time' => $existingEnd,
        ]);

        $newStart = Carbon::now()->addDay()->setHour(11)->setMinute(0)->setSecond(0);
        $newEnd = Carbon::now()->addDay()->setHour(12)->setMinute(0)->setSecond(1);

        expect($this->service->isClash($this->room->id, $newStart, $newEnd))->toBeTrue();
    });

    test('isClash handles exact same time as existing booking', function () {
        $existingStart = Carbon::now()->addDay()->setHour(10)->setMinute(0)->setSecond(0);
        $existingEnd = Carbon::now()->addDay()->setHour(12)->setMinute(0)->setSecond(0);

        Booking::factory()->create([
            'room_id' => $this->room->id,
            'start_time' => $existingStart,
            'end_time' => $existingEnd,
        ]);

        expect($this->service->isClash($this->room->id, $existingStart, $existingEnd))->toBeTrue();
    });
});

describe('BookingService Room Isolation Tests', function () {
    test('isClash only checks bookings for the specified room', function () {
        $otherRoom = Room::factory()->create();

        $existingStart = Carbon::now()->addDay()->setHour(10)->setMinute(0);
        $existingEnd = Carbon::now()->addDay()->setHour(12)->setMinute(0);

        Booking::factory()->create([
            'room_id' => $otherRoom->id,
            'start_time' => $existingStart,
            'end_time' => $existingEnd,
        ]);

        $newStart = Carbon::now()->addDay()->setHour(11)->setMinute(0);
        $newEnd = Carbon::now()->addDay()->setHour(13)->setMinute(0);

        expect($this->service->isClash($this->room->id, $newStart, $newEnd))->toBeFalse();
    });

    test('isClash detects clash in specific room even with bookings in other rooms', function () {
        $otherRoom = Room::factory()->create();

        // Booking in other room (should not cause clash)
        Booking::factory()->create([
            'room_id' => $otherRoom->id,
            'start_time' => Carbon::now()->addDay()->setHour(10),
            'end_time' => Carbon::now()->addDay()->setHour(12),
        ]);

        // Booking in target room (should cause clash)
        Booking::factory()->create([
            'room_id' => $this->room->id,
            'start_time' => Carbon::now()->addDay()->setHour(10),
            'end_time' => Carbon::now()->addDay()->setHour(12),
        ]);

        $newStart = Carbon::now()->addDay()->setHour(11);
        $newEnd = Carbon::now()->addDay()->setHour(13);

        expect($this->service->isClash($this->room->id, $newStart, $newEnd))->toBeTrue();
    });

    test('isClash handles same time slots across multiple rooms independently', function () {
        $room1 = Room::factory()->create();
        $room2 = Room::factory()->create();
        $room3 = Room::factory()->create();

        $startTime = Carbon::now()->addDay()->setHour(10);
        $endTime = Carbon::now()->addDay()->setHour(12);

        // Book same time in room1
        Booking::factory()->create([
            'room_id' => $room1->id,
            'start_time' => $startTime,
            'end_time' => $endTime,
        ]);

        // Can still book same time in room2 and room3
        expect($this->service->isClash($room2->id, $startTime, $endTime))->toBeFalse()
            ->and($this->service->isClash($room3->id, $startTime, $endTime))->toBeFalse();

        // But cannot book again in room1
        expect($this->service->isClash($room1->id, $startTime, $endTime))->toBeTrue();
    });
});

describe('BookingService Multiple Bookings Tests', function () {
    test('isClash handles multiple existing bookings correctly with no clash', function () {
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

    test('isClash can fit booking between multiple existing bookings', function () {
        // Booking 1: 09:00 - 10:00
        Booking::factory()->create([
            'room_id' => $this->room->id,
            'start_time' => Carbon::now()->addDay()->setHour(9)->setMinute(0),
            'end_time' => Carbon::now()->addDay()->setHour(10)->setMinute(0),
        ]);

        // Booking 2: 12:00 - 14:00
        Booking::factory()->create([
            'room_id' => $this->room->id,
            'start_time' => Carbon::now()->addDay()->setHour(12)->setMinute(0),
            'end_time' => Carbon::now()->addDay()->setHour(14)->setMinute(0),
        ]);

        // Booking 3: 16:00 - 18:00
        Booking::factory()->create([
            'room_id' => $this->room->id,
            'start_time' => Carbon::now()->addDay()->setHour(16)->setMinute(0),
            'end_time' => Carbon::now()->addDay()->setHour(18)->setMinute(0),
        ]);

        // New booking: 10:00 - 12:00 (fits between 1 and 2)
        $newStart = Carbon::now()->addDay()->setHour(10)->setMinute(0);
        $newEnd = Carbon::now()->addDay()->setHour(12)->setMinute(0);

        expect($this->service->isClash($this->room->id, $newStart, $newEnd))->toBeFalse();
    });

    test('isClash detects clash when new booking spans multiple existing bookings', function () {
        // Booking 1: 10:00 - 11:00
        Booking::factory()->create([
            'room_id' => $this->room->id,
            'start_time' => Carbon::now()->addDay()->setHour(10),
            'end_time' => Carbon::now()->addDay()->setHour(11),
        ]);

        // Booking 2: 13:00 - 14:00
        Booking::factory()->create([
            'room_id' => $this->room->id,
            'start_time' => Carbon::now()->addDay()->setHour(13),
            'end_time' => Carbon::now()->addDay()->setHour(14),
        ]);

        // New booking: 09:00 - 15:00 (overlaps both)
        $newStart = Carbon::now()->addDay()->setHour(9);
        $newEnd = Carbon::now()->addDay()->setHour(15);

        expect($this->service->isClash($this->room->id, $newStart, $newEnd))->toBeTrue();
    });
});

describe('BookingService Booking Status Tests', function () {
    test('isClash only checks pending and approved bookings, not rejected', function () {
        // Pending booking
        Booking::factory()->pending()->create([
            'room_id' => $this->room->id,
            'start_time' => Carbon::now()->addDay()->setHour(10),
            'end_time' => Carbon::now()->addDay()->setHour(11),
        ]);

        // Approved booking
        Booking::factory()->approved()->create([
            'room_id' => $this->room->id,
            'start_time' => Carbon::now()->addDay()->setHour(13),
            'end_time' => Carbon::now()->addDay()->setHour(14),
        ]);

        // Rejected booking
        Booking::factory()->rejected()->create([
            'room_id' => $this->room->id,
            'start_time' => Carbon::now()->addDay()->setHour(16),
            'end_time' => Carbon::now()->addDay()->setHour(17),
        ]);

        // Should detect clash with pending
        expect($this->service->isClash($this->room->id,
            Carbon::now()->addDay()->setHour(10),
            Carbon::now()->addDay()->setHour(11)
        ))->toBeTrue();

        // Should detect clash with approved
        expect($this->service->isClash($this->room->id,
            Carbon::now()->addDay()->setHour(13),
            Carbon::now()->addDay()->setHour(14)
        ))->toBeTrue();

        // Should NOT detect clash with rejected (rejected bookings don't block slots)
        expect($this->service->isClash($this->room->id,
            Carbon::now()->addDay()->setHour(16),
            Carbon::now()->addDay()->setHour(17)
        ))->toBeFalse();
    });
});

describe('BookingService Date Range Tests', function () {
    test('isClash handles bookings on different days', function () {
        $today = Carbon::now()->addDay();
        $tomorrow = Carbon::now()->addDays(2);

        // Booking today 10:00 - 12:00
        Booking::factory()->create([
            'room_id' => $this->room->id,
            'start_time' => $today->copy()->setHour(10),
            'end_time' => $today->copy()->setHour(12),
        ]);

        // New booking tomorrow 10:00 - 12:00 (same time, different day)
        $newStart = $tomorrow->copy()->setHour(10);
        $newEnd = $tomorrow->copy()->setHour(12);

        expect($this->service->isClash($this->room->id, $newStart, $newEnd))->toBeFalse();
    });

    test('isClash handles multi-day bookings', function () {
        $day1 = Carbon::now()->addDay()->setHour(22); // 22:00 today
        $day2 = Carbon::now()->addDays(2)->setHour(2); // 02:00 next day

        Booking::factory()->create([
            'room_id' => $this->room->id,
            'start_time' => $day1,
            'end_time' => $day2,
        ]);

        // Booking during the night should clash
        $newStart = Carbon::now()->addDay()->setHour(23);
        $newEnd = Carbon::now()->addDays(2)->setHour(1);

        expect($this->service->isClash($this->room->id, $newStart, $newEnd))->toBeTrue();
    });

    test('isClash handles bookings across month boundaries', function () {
        // Last day of month
        $endOfMonth = Carbon::create(2025, 1, 31, 22, 0, 0);
        // First day of next month
        $startOfNextMonth = Carbon::create(2025, 2, 1, 2, 0, 0);

        Booking::factory()->create([
            'room_id' => $this->room->id,
            'start_time' => $endOfMonth,
            'end_time' => $startOfNextMonth,
        ]);

        // Should detect clash
        $newStart = $endOfMonth->copy()->addHour(1);
        $newEnd = $startOfNextMonth->copy()->subHour(1);

        expect($this->service->isClash($this->room->id, $newStart, $newEnd))->toBeTrue();
    });
});

describe('BookingService Edge Cases', function () {
    test('isClash handles very short bookings (15 minutes)', function () {
        Booking::factory()->create([
            'room_id' => $this->room->id,
            'start_time' => Carbon::now()->addDay()->setHour(10)->setMinute(0),
            'end_time' => Carbon::now()->addDay()->setHour(10)->setMinute(15),
        ]);

        // Should not clash right after
        $newStart = Carbon::now()->addDay()->setHour(10)->setMinute(15);
        $newEnd = Carbon::now()->addDay()->setHour(10)->setMinute(30);

        expect($this->service->isClash($this->room->id, $newStart, $newEnd))->toBeFalse();

        // Should clash if overlapping
        $newStart = Carbon::now()->addDay()->setHour(10)->setMinute(10);
        $newEnd = Carbon::now()->addDay()->setHour(10)->setMinute(20);

        expect($this->service->isClash($this->room->id, $newStart, $newEnd))->toBeTrue();
    });

    test('isClash handles very long bookings (8+ hours)', function () {
        Booking::factory()->create([
            'room_id' => $this->room->id,
            'start_time' => Carbon::now()->addDay()->setHour(9),
            'end_time' => Carbon::now()->addDay()->setHour(18), // 9 hours
        ]);

        // Should clash anywhere in the middle
        $newStart = Carbon::now()->addDay()->setHour(12);
        $newEnd = Carbon::now()->addDay()->setHour(13);

        expect($this->service->isClash($this->room->id, $newStart, $newEnd))->toBeTrue();
    });

    test('isClash handles back-to-back bookings throughout the day', function () {
        // Fill the day with back-to-back bookings
        Booking::factory()->create([
            'room_id' => $this->room->id,
            'start_time' => Carbon::now()->addDay()->setHour(9)->setMinute(0)->setSecond(0),
            'end_time' => Carbon::now()->addDay()->setHour(10)->setMinute(0)->setSecond(0),
        ]);

        Booking::factory()->create([
            'room_id' => $this->room->id,
            'start_time' => Carbon::now()->addDay()->setHour(10)->setMinute(0)->setSecond(0),
            'end_time' => Carbon::now()->addDay()->setHour(11)->setMinute(0)->setSecond(0),
        ]);

        Booking::factory()->create([
            'room_id' => $this->room->id,
            'start_time' => Carbon::now()->addDay()->setHour(11)->setMinute(0)->setSecond(0),
            'end_time' => Carbon::now()->addDay()->setHour(12)->setMinute(0)->setSecond(0),
        ]);

        // No clash before first booking
        expect($this->service->isClash($this->room->id,
            Carbon::now()->addDay()->setHour(8)->setMinute(0)->setSecond(0),
            Carbon::now()->addDay()->setHour(9)->setMinute(0)->setSecond(0)
        ))->toBeFalse();

        // No clash after last booking
        expect($this->service->isClash($this->room->id,
            Carbon::now()->addDay()->setHour(12)->setMinute(0)->setSecond(0),
            Carbon::now()->addDay()->setHour(13)->setMinute(0)->setSecond(0)
        ))->toBeFalse();

        // Clash if trying to book during any slot
        expect($this->service->isClash($this->room->id,
            Carbon::now()->addDay()->setHour(10)->setMinute(30),
            Carbon::now()->addDay()->setHour(11)->setMinute(30)
        ))->toBeTrue();
    });

    test('isClash performance with many existing bookings', function () {
        // Create 50 bookings throughout the month
        for ($day = 1; $day <= 25; $day++) {
            Booking::factory()->create([
                'room_id' => $this->room->id,
                'start_time' => Carbon::now()->addDays($day)->setHour(10),
                'end_time' => Carbon::now()->addDays($day)->setHour(12),
            ]);

            Booking::factory()->create([
                'room_id' => $this->room->id,
                'start_time' => Carbon::now()->addDays($day)->setHour(14),
                'end_time' => Carbon::now()->addDays($day)->setHour(16),
            ]);
        }

        // Should still correctly detect no clash
        $newStart = Carbon::now()->addDays(5)->setHour(13)->setMinute(0)->setSecond(0);
        $newEnd = Carbon::now()->addDays(5)->setHour(14)->setMinute(0)->setSecond(0);

        expect($this->service->isClash($this->room->id, $newStart, $newEnd))->toBeFalse();

        // Should still correctly detect clash
        $newStart = Carbon::now()->addDays(5)->setHour(11);
        $newEnd = Carbon::now()->addDays(5)->setHour(13);

        expect($this->service->isClash($this->room->id, $newStart, $newEnd))->toBeTrue();
    });
});

describe('BookingService Timezone Considerations', function () {
    test('isClash handles times in different hours but same UTC time', function () {
        // Assuming Asia/Kuala_Lumpur timezone (UTC+8)
        $startTime = Carbon::create(2025, 6, 15, 10, 0, 0, 'Asia/Kuala_Lumpur');
        $endTime = Carbon::create(2025, 6, 15, 12, 0, 0, 'Asia/Kuala_Lumpur');

        Booking::factory()->create([
            'room_id' => $this->room->id,
            'start_time' => $startTime,
            'end_time' => $endTime,
        ]);

        // Same absolute time, should clash
        $newStart = Carbon::create(2025, 6, 15, 10, 0, 0, 'Asia/Kuala_Lumpur');
        $newEnd = Carbon::create(2025, 6, 15, 12, 0, 0, 'Asia/Kuala_Lumpur');

        expect($this->service->isClash($this->room->id, $newStart, $newEnd))->toBeTrue();
    });
});
