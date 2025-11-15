<?php

use App\Models\Booking;
use App\Models\Role;
use App\Models\Room;
use App\Models\User;

beforeEach(function () {
    Role::firstOrCreate(['id' => 1], ['name' => 'Admin']);
    Role::firstOrCreate(['id' => 2], ['name' => 'Regular User']);

    $this->admin = User::factory()->create(['role_id' => 1]);
    $this->user = User::factory()->create(['role_id' => 2]);
    $this->otherUser = User::factory()->create(['role_id' => 2]);
    $this->room = Room::factory()->create();
});

describe('Mass Assignment Protection for Booking Creation', function () {
    test('user cannot set their own booking status to approved during creation', function () {
        $this->actingAs($this->user);

        $response = $this->post(route('bookings.store'), [
            'room_id' => $this->room->id,
            'start_time' => now()->addDay(),
            'end_time' => now()->addDay()->addHours(2),
            'number_of_student' => 20,
            'purpose' => 'Meeting',
            'status' => true, // Trying to self-approve
        ]);

        $booking = Booking::latest()->first();

        // Status should be null (pending), not true (approved)
        expect($booking->status)->toBeNull();
    });

    test('user cannot assign booking to another user during creation', function () {
        $this->actingAs($this->user);

        $response = $this->post(route('bookings.store'), [
            'room_id' => $this->room->id,
            'start_time' => now()->addDay(),
            'end_time' => now()->addDay()->addHours(2),
            'number_of_student' => 20,
            'purpose' => 'Meeting',
            'user_id' => $this->otherUser->id, // Trying to create for other user
        ]);

        $booking = Booking::latest()->first();

        // Booking should belong to authenticated user, not other user
        expect($booking->user_id)->toBe($this->user->id)
            ->and($booking->user_id)->not->toBe($this->otherUser->id);
    });

    test('user cannot set rejection_reason during creation', function () {
        $this->actingAs($this->user);

        $this->post(route('bookings.store'), [
            'room_id' => $this->room->id,
            'start_time' => now()->addDay(),
            'end_time' => now()->addDay()->addHours(2),
            'number_of_student' => 20,
            'purpose' => 'Meeting',
            'rejection_reason' => 'Should not be set', // Malicious attempt
        ]);

        $booking = Booking::latest()->first();

        expect($booking->rejection_reason)->toBeNull();
    });
});

describe('Mass Assignment Protection for Booking Updates', function () {
    test('user cannot change booking status through update', function () {
        $booking = Booking::factory()->pending()->create([
            'user_id' => $this->user->id,
        ]);

        $this->actingAs($this->user);

        $this->put(route('bookings.update', $booking), [
            'room_id' => $booking->room_id,
            'start_time' => $booking->start_time,
            'end_time' => $booking->end_time,
            'number_of_student' => 30,
            'purpose' => 'Updated meeting',
            'status' => true, // Trying to self-approve
        ]);

        $booking->refresh();

        // Status should still be null (pending)
        expect($booking->status)->toBeNull();
    });

    test('user cannot change booking owner through update', function () {
        $booking = Booking::factory()->pending()->create([
            'user_id' => $this->user->id,
        ]);

        $this->actingAs($this->user);

        $this->put(route('bookings.update', $booking), [
            'room_id' => $booking->room_id,
            'start_time' => $booking->start_time,
            'end_time' => $booking->end_time,
            'number_of_student' => 30,
            'purpose' => 'Updated meeting',
            'user_id' => $this->otherUser->id, // Trying to transfer ownership
        ]);

        $booking->refresh();

        // Owner should not change
        expect($booking->user_id)->toBe($this->user->id)
            ->and($booking->user_id)->not->toBe($this->otherUser->id);
    });

    test('user cannot add rejection_reason through update', function () {
        $booking = Booking::factory()->pending()->create([
            'user_id' => $this->user->id,
        ]);

        $this->actingAs($this->user);

        $this->put(route('bookings.update', $booking), [
            'room_id' => $booking->room_id,
            'start_time' => $booking->start_time,
            'end_time' => $booking->end_time,
            'number_of_student' => 30,
            'purpose' => 'Updated meeting',
            'rejection_reason' => 'Self-rejection', // Malicious attempt
        ]);

        $booking->refresh();

        expect($booking->rejection_reason)->toBeNull();
    });

    test('user cannot update primary key id', function () {
        $booking = Booking::factory()->pending()->create([
            'user_id' => $this->user->id,
        ]);

        $originalId = $booking->id;

        $this->actingAs($this->user);

        $this->put(route('bookings.update', $booking), [
            'id' => 99999, // Trying to change ID
            'room_id' => $booking->room_id,
            'start_time' => $booking->start_time,
            'end_time' => $booking->end_time,
            'number_of_student' => 30,
            'purpose' => 'Updated meeting',
        ]);

        $booking->refresh();

        expect($booking->id)->toBe($originalId)
            ->and($booking->id)->not->toBe(99999);
    });

    test('user cannot update timestamps manually', function () {
        $booking = Booking::factory()->pending()->create([
            'user_id' => $this->user->id,
        ]);

        $originalCreatedAt = $booking->created_at;
        $fakeCreatedAt = now()->subYears(5);

        $this->actingAs($this->user);

        $this->put(route('bookings.update', $booking), [
            'room_id' => $booking->room_id,
            'start_time' => $booking->start_time,
            'end_time' => $booking->end_time,
            'number_of_student' => 30,
            'purpose' => 'Updated meeting',
            'created_at' => $fakeCreatedAt, // Trying to fake creation date
        ]);

        $booking->refresh();

        expect($booking->created_at->equalTo($originalCreatedAt))->toBeTrue()
            ->and($booking->created_at->equalTo($fakeCreatedAt))->toBeFalse();
    });
});

describe('Mass Assignment Protection for Room', function () {
    test('admin cannot manipulate room id during creation', function () {
        $this->actingAs($this->admin);

        $response = $this->post(route('rooms.store'), [
            'id' => 99999, // Trying to set specific ID
            'name' => 'New Room',
            'description' => 'Test room',
        ]);

        $room = Room::where('name', 'New Room')->first();

        if ($room) {
            expect($room->id)->not->toBe(99999);
        }
    });

    test('admin cannot manipulate room timestamps during creation', function () {
        $this->actingAs($this->admin);

        $fakeDate = now()->subYears(10);

        $this->post(route('rooms.store'), [
            'name' => 'Timestamped Room',
            'description' => 'Test room',
            'created_at' => $fakeDate,
            'updated_at' => $fakeDate,
        ]);

        $room = Room::where('name', 'Timestamped Room')->first();

        if ($room) {
            expect($room->created_at->greaterThan($fakeDate))->toBeTrue();
        }
    });
});

describe('Mass Assignment Protection for SQL Injection', function () {
    test('cannot inject SQL through booking purpose field', function () {
        $this->actingAs($this->user);

        $maliciousInput = "'; DROP TABLE bookings; --";

        $this->post(route('bookings.store'), [
            'room_id' => $this->room->id,
            'start_time' => now()->addDay(),
            'end_time' => now()->addDay()->addHours(2),
            'number_of_student' => 20,
            'purpose' => $maliciousInput,
        ]);

        // Bookings table should still exist
        expect(Booking::count())->toBeGreaterThan(0);

        // The malicious input should be stored as plain text
        $booking = Booking::latest()->first();
        expect($booking->purpose)->toBe($maliciousInput);
    });

    test('cannot inject SQL through room name field', function () {
        $this->actingAs($this->admin);

        $maliciousInput = "'; DELETE FROM rooms WHERE '1'='1";

        $this->post(route('rooms.store'), [
            'name' => $maliciousInput,
            'description' => 'Test',
        ]);

        // All rooms should still exist
        expect(Room::count())->toBeGreaterThan(0);
    });
});

describe('Mass Assignment Protection for XSS', function () {
    test('booking purpose with XSS attempt is safely stored', function () {
        $this->actingAs($this->user);

        $xssInput = '<script>alert("XSS")</script>';

        $this->post(route('bookings.store'), [
            'room_id' => $this->room->id,
            'start_time' => now()->addDay(),
            'end_time' => now()->addDay()->addHours(2),
            'number_of_student' => 20,
            'purpose' => $xssInput,
        ]);

        $booking = Booking::latest()->first();

        // The XSS attempt should be stored as plain text
        expect($booking->purpose)->toBe($xssInput);
    });

    test('room description with XSS attempt is safely stored', function () {
        $this->actingAs($this->admin);

        $xssInput = '<img src=x onerror="alert(1)">';

        $this->post(route('rooms.store'), [
            'name' => 'XSS Test Room',
            'description' => $xssInput,
        ]);

        $room = Room::where('name', 'XSS Test Room')->first();

        if ($room) {
            expect($room->description)->toBe($xssInput);
        }
    });
});
