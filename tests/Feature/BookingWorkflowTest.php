<?php

use App\Models\Booking;
use App\Models\Role;
use App\Models\Room;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

beforeEach(function () {
    // Ensure roles exist
    Role::firstOrCreate(['id' => 1], ['name' => 'Admin']);
    Role::firstOrCreate(['id' => 2], ['name' => 'Regular User']);

    $this->admin = User::factory()->create(['role_id' => 1]);
    $this->user = User::factory()->create(['role_id' => 2]);
    $this->room = Room::factory()->create();

    Mail::fake();
});

describe('Booking Creation Workflow', function () {
    test('user can create a booking successfully', function () {
        $this->actingAs($this->user);

        $response = $this->post(route('bookings.store'), [
            'room_id' => $this->room->id,
            'start_time' => now()->addDay()->format('Y-m-d H:i:s'),
            'end_time' => now()->addDay()->addHours(2)->format('Y-m-d H:i:s'),
            'number_of_student' => 20,
            'purpose' => 'Team meeting',
            'equipment_needed' => 'Projector',
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('bookings', [
            'user_id' => $this->user->id,
            'room_id' => $this->room->id,
            'purpose' => 'Team meeting',
            'status' => null, // Pending
        ]);
    });

    test('newly created booking has pending status', function () {
        $this->actingAs($this->user);

        $this->post(route('bookings.store'), [
            'room_id' => $this->room->id,
            'start_time' => now()->addDay(),
            'end_time' => now()->addDay()->addHours(2),
            'number_of_student' => 20,
            'purpose' => 'Team meeting',
        ]);

        $booking = Booking::latest()->first();

        expect($booking->status)->toBeNull();
    });

    test('booking belongs to authenticated user', function () {
        $this->actingAs($this->user);

        $this->post(route('bookings.store'), [
            'room_id' => $this->room->id,
            'start_time' => now()->addDay(),
            'end_time' => now()->addDay()->addHours(2),
            'number_of_student' => 20,
            'purpose' => 'Team meeting',
        ]);

        $booking = Booking::latest()->first();

        expect($booking->user_id)->toBe($this->user->id);
    });
});

describe('Booking Approval Workflow', function () {
    test('admin can approve pending booking', function () {
        $booking = Booking::factory()->pending()->create([
            'user_id' => $this->user->id,
            'room_id' => $this->room->id,
        ]);

        $this->actingAs($this->admin);

        $response = $this->post(route('bookings.approve', $booking));

        $response->assertRedirect();

        $booking->refresh();
        expect($booking->status)->toBeTrue();
    });

    test('regular user cannot approve bookings', function () {
        $booking = Booking::factory()->pending()->create([
            'user_id' => $this->user->id,
        ]);

        $this->actingAs($this->user);

        $response = $this->post(route('bookings.approve', $booking));

        $response->assertForbidden();

        $booking->refresh();
        expect($booking->status)->toBeNull(); // Still pending
    });

    test('approved booking sends email notification', function () {
        $booking = Booking::factory()->pending()->create([
            'user_id' => $this->user->id,
        ]);

        $this->actingAs($this->admin);

        $this->post(route('bookings.approve', $booking));

        Mail::assertQueued(\App\Mail\BookingApproved::class, function ($mail) use ($booking) {
            return $mail->hasTo($booking->user->email);
        });
    });
});

describe('Booking Rejection Workflow', function () {
    test('admin can reject pending booking with reason', function () {
        $booking = Booking::factory()->pending()->create([
            'user_id' => $this->user->id,
        ]);

        $this->actingAs($this->admin);

        $response = $this->post(route('bookings.reject', $booking), [
            'rejection_reason' => 'Room is under maintenance',
        ]);

        $response->assertRedirect();

        $booking->refresh();
        expect($booking->status)->toBeFalse()
            ->and($booking->rejection_reason)->toBe('Room is under maintenance');
    });

    test('regular user cannot reject bookings', function () {
        $booking = Booking::factory()->pending()->create([
            'user_id' => $this->user->id,
        ]);

        $this->actingAs($this->user);

        $response = $this->post(route('bookings.reject', $booking), [
            'rejection_reason' => 'Changed my mind',
        ]);

        $response->assertForbidden();

        $booking->refresh();
        expect($booking->status)->toBeNull(); // Still pending
    });

    test('rejected booking sends email notification', function () {
        $booking = Booking::factory()->pending()->create([
            'user_id' => $this->user->id,
        ]);

        $this->actingAs($this->admin);

        $this->post(route('bookings.reject', $booking), [
            'rejection_reason' => 'Room not available',
        ]);

        Mail::assertQueued(\App\Mail\BookingRejected::class, function ($mail) use ($booking) {
            return $mail->hasTo($booking->user->email);
        });
    });
});

describe('Booking Update Workflow', function () {
    test('user can update their own pending booking', function () {
        $booking = Booking::factory()->pending()->create([
            'user_id' => $this->user->id,
            'purpose' => 'Original purpose',
        ]);

        $this->actingAs($this->user);

        $response = $this->put(route('bookings.update', $booking), [
            'room_id' => $booking->room_id,
            'start_time' => $booking->start_time,
            'end_time' => $booking->end_time,
            'number_of_student' => 30,
            'purpose' => 'Updated purpose',
        ]);

        $response->assertRedirect();

        $booking->refresh();
        expect($booking->purpose)->toBe('Updated purpose')
            ->and($booking->number_of_student)->toBe(30);
    });

    test('user cannot update approved booking', function () {
        $booking = Booking::factory()->approved()->create([
            'user_id' => $this->user->id,
            'purpose' => 'Original purpose',
        ]);

        $this->actingAs($this->user);

        $response = $this->put(route('bookings.update', $booking), [
            'room_id' => $booking->room_id,
            'start_time' => $booking->start_time,
            'end_time' => $booking->end_time,
            'number_of_student' => 30,
            'purpose' => 'Updated purpose',
        ]);

        $response->assertForbidden();
    });

    test('user cannot update another users booking', function () {
        $otherUser = User::factory()->create(['role_id' => 2]);
        $booking = Booking::factory()->pending()->create([
            'user_id' => $otherUser->id,
        ]);

        $this->actingAs($this->user);

        $response = $this->put(route('bookings.update', $booking), [
            'room_id' => $booking->room_id,
            'start_time' => $booking->start_time,
            'end_time' => $booking->end_time,
            'number_of_student' => 30,
            'purpose' => 'Malicious update',
        ]);

        $response->assertForbidden();
    });

    test('admin can update any booking', function () {
        $booking = Booking::factory()->approved()->create([
            'user_id' => $this->user->id,
            'purpose' => 'Original purpose',
        ]);

        $this->actingAs($this->admin);

        $response = $this->put(route('bookings.update', $booking), [
            'room_id' => $booking->room_id,
            'start_time' => $booking->start_time,
            'end_time' => $booking->end_time,
            'number_of_student' => 50,
            'purpose' => 'Admin updated purpose',
        ]);

        $response->assertRedirect();

        $booking->refresh();
        expect($booking->purpose)->toBe('Admin updated purpose');
    });
});

describe('Booking Delete Workflow', function () {
    test('user can delete their own booking', function () {
        $booking = Booking::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $this->actingAs($this->user);

        $response = $this->delete(route('bookings.destroy', $booking));

        $response->assertRedirect();

        $this->assertDatabaseMissing('bookings', [
            'id' => $booking->id,
        ]);
    });

    test('user cannot delete another users booking', function () {
        $otherUser = User::factory()->create(['role_id' => 2]);
        $booking = Booking::factory()->create([
            'user_id' => $otherUser->id,
        ]);

        $this->actingAs($this->user);

        $response = $this->delete(route('bookings.destroy', $booking));

        $response->assertForbidden();

        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
        ]);
    });

    test('admin can delete any booking', function () {
        $booking = Booking::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $this->actingAs($this->admin);

        $response = $this->delete(route('bookings.destroy', $booking));

        $response->assertRedirect();

        $this->assertDatabaseMissing('bookings', [
            'id' => $booking->id,
        ]);
    });
});

describe('Complete Booking Lifecycle Workflow', function () {
    test('complete workflow from creation to approval', function () {
        // Step 1: User creates a booking
        $this->actingAs($this->user);

        $this->post(route('bookings.store'), [
            'room_id' => $this->room->id,
            'start_time' => now()->addDay(),
            'end_time' => now()->addDay()->addHours(2),
            'number_of_student' => 25,
            'purpose' => 'Important meeting',
            'equipment_needed' => 'Projector',
        ]);

        $booking = Booking::latest()->first();

        // Verify booking is pending
        expect($booking->status)->toBeNull()
            ->and($booking->user_id)->toBe($this->user->id);

        // Step 2: Admin approves the booking
        $this->actingAs($this->admin);

        $this->post(route('bookings.approve', $booking));

        $booking->refresh();

        // Verify booking is approved
        expect($booking->status)->toBeTrue();

        // Verify email was sent
        Mail::assertQueued(\App\Mail\BookingApproved::class);
    });

    test('complete workflow from creation to rejection', function () {
        // Step 1: User creates a booking
        $this->actingAs($this->user);

        $this->post(route('bookings.store'), [
            'room_id' => $this->room->id,
            'start_time' => now()->addDay(),
            'end_time' => now()->addDay()->addHours(2),
            'number_of_student' => 25,
            'purpose' => 'Meeting',
        ]);

        $booking = Booking::latest()->first();

        // Step 2: Admin rejects the booking
        $this->actingAs($this->admin);

        $this->post(route('bookings.reject', $booking), [
            'rejection_reason' => 'Room is unavailable',
        ]);

        $booking->refresh();

        // Verify booking is rejected
        expect($booking->status)->toBeFalse()
            ->and($booking->rejection_reason)->toBe('Room is unavailable');

        // Verify email was sent
        Mail::assertQueued(\App\Mail\BookingRejected::class);
    });

    test('user creates booking, updates it, then admin approves', function () {
        // Step 1: User creates booking
        $this->actingAs($this->user);

        $this->post(route('bookings.store'), [
            'room_id' => $this->room->id,
            'start_time' => now()->addDay(),
            'end_time' => now()->addDay()->addHours(2),
            'number_of_student' => 20,
            'purpose' => 'Initial purpose',
        ]);

        $booking = Booking::latest()->first();

        // Step 2: User updates booking (still pending)
        $this->put(route('bookings.update', $booking), [
            'room_id' => $this->room->id,
            'start_time' => now()->addDay(),
            'end_time' => now()->addDay()->addHours(3), // Extended
            'number_of_student' => 30, // More students
            'purpose' => 'Updated purpose',
        ]);

        $booking->refresh();

        expect($booking->purpose)->toBe('Updated purpose')
            ->and($booking->number_of_student)->toBe(30)
            ->and($booking->status)->toBeNull(); // Still pending

        // Step 3: Admin approves updated booking
        $this->actingAs($this->admin);

        $this->post(route('bookings.approve', $booking));

        $booking->refresh();

        expect($booking->status)->toBeTrue();
    });

    test('user creates booking then cancels it', function () {
        // Step 1: User creates booking
        $this->actingAs($this->user);

        $this->post(route('bookings.store'), [
            'room_id' => $this->room->id,
            'start_time' => now()->addDay(),
            'end_time' => now()->addDay()->addHours(2),
            'number_of_student' => 20,
            'purpose' => 'Meeting',
        ]);

        $booking = Booking::latest()->first();

        // Step 2: User cancels (deletes) booking
        $this->delete(route('bookings.destroy', $booking));

        $this->assertDatabaseMissing('bookings', [
            'id' => $booking->id,
        ]);
    });
});

describe('Booking Clash Prevention Workflow', function () {
    test('cannot create booking that clashes with existing booking', function () {
        // Create existing booking
        Booking::factory()->approved()->create([
            'room_id' => $this->room->id,
            'start_time' => now()->addDay()->setHour(10),
            'end_time' => now()->addDay()->setHour(12),
        ]);

        // Attempt to create overlapping booking
        $this->actingAs($this->user);

        $response = $this->post(route('bookings.store'), [
            'room_id' => $this->room->id,
            'start_time' => now()->addDay()->setHour(11), // Overlaps
            'end_time' => now()->addDay()->setHour(13),
            'number_of_student' => 20,
            'purpose' => 'Conflicting meeting',
        ]);

        $response->assertSessionHasErrors(); // Or redirect with error message

        // Verify booking was not created
        $count = Booking::where('purpose', 'Conflicting meeting')->count();
        expect($count)->toBe(0);
    });

    test('can create booking in different room at same time', function () {
        $otherRoom = Room::factory()->create();

        // Create booking in room 1
        Booking::factory()->create([
            'room_id' => $this->room->id,
            'start_time' => now()->addDay()->setHour(10),
            'end_time' => now()->addDay()->setHour(12),
        ]);

        // Create booking in room 2 at same time
        $this->actingAs($this->user);

        $response = $this->post(route('bookings.store'), [
            'room_id' => $otherRoom->id,
            'start_time' => now()->addDay()->setHour(10),
            'end_time' => now()->addDay()->setHour(12),
            'number_of_student' => 20,
            'purpose' => 'Simultaneous meeting',
        ]);

        $response->assertRedirect(); // Success

        $this->assertDatabaseHas('bookings', [
            'room_id' => $otherRoom->id,
            'purpose' => 'Simultaneous meeting',
        ]);
    });

    test('can create back-to-back bookings without clash', function () {
        // Create first booking 10:00-12:00
        Booking::factory()->create([
            'room_id' => $this->room->id,
            'start_time' => now()->addDay()->setHour(10)->setMinute(0),
            'end_time' => now()->addDay()->setHour(12)->setMinute(0),
        ]);

        // Create second booking 12:00-14:00 (starts when first ends)
        $this->actingAs($this->user);

        $response = $this->post(route('bookings.store'), [
            'room_id' => $this->room->id,
            'start_time' => now()->addDay()->setHour(12)->setMinute(0)->format('Y-m-d H:i:s'),
            'end_time' => now()->addDay()->setHour(14)->setMinute(0)->format('Y-m-d H:i:s'),
            'number_of_student' => 20,
            'purpose' => 'Back-to-back meeting',
        ]);

        $response->assertRedirect(); // Success

        $this->assertDatabaseHas('bookings', [
            'purpose' => 'Back-to-back meeting',
        ]);
    });
});
