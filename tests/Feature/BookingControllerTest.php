<?php

use App\Models\Booking;
use App\Models\Room;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\BookingConfirmationMail;
use App\Mail\BookingApprovedMail;
use App\Mail\BookingRejectedMail;

beforeEach(function () {
    $this->admin = User::factory()->create(['role_id' => 1]);
    $this->regularUser = User::factory()->create(['role_id' => 2]);
    $this->room = Room::factory()->create();
});

test('admin can view all bookings', function () {
    $this->actingAs($this->admin);

    $adminBooking = Booking::factory()->create(['user_id' => $this->admin->id]);
    $userBooking = Booking::factory()->create(['user_id' => $this->regularUser->id]);

    $response = $this->get(route('bookings.index'));

    $response->assertStatus(200)
        ->assertSee($adminBooking->purpose)
        ->assertSee($userBooking->purpose);
});

test('regular user can only view their own bookings', function () {
    $this->actingAs($this->regularUser);

    $theirBooking = Booking::factory()->create(['user_id' => $this->regularUser->id]);
    $otherBooking = Booking::factory()->create(['user_id' => $this->admin->id]);

    $response = $this->get(route('bookings.index'));

    $response->assertStatus(200)
        ->assertSee($theirBooking->purpose)
        ->assertDontSee($otherBooking->purpose);
});

test('authenticated user can create a booking', function () {
    Mail::fake();

    $this->actingAs($this->regularUser);

    $startTime = Carbon::now()->addDay()->setHour(10);
    $endTime = Carbon::now()->addDay()->setHour(12);

    $response = $this->post(route('bookings.store'), [
        'room_id' => $this->room->id,
        'start_time' => $startTime->toDateTimeString(),
        'end_time' => $endTime->toDateTimeString(),
        'number_of_student' => 25,
        'equipment_needed' => 'Projector',
        'purpose' => 'Team meeting',
    ]);

    $response->assertRedirect(route('dashboard'));

    $this->assertDatabaseHas('bookings', [
        'room_id' => $this->room->id,
        'user_id' => $this->regularUser->id,
        'purpose' => 'Team meeting',
        'status' => null,
    ]);

    Mail::assertSent(BookingConfirmationMail::class);
});

test('booking creation fails when time slot conflicts with existing booking', function () {
    $this->actingAs($this->regularUser);

    $existingStart = Carbon::now()->addDay()->setHour(10);
    $existingEnd = Carbon::now()->addDay()->setHour(12);

    Booking::factory()->create([
        'room_id' => $this->room->id,
        'start_time' => $existingStart,
        'end_time' => $existingEnd,
    ]);

    $response = $this->post(route('bookings.store'), [
        'room_id' => $this->room->id,
        'start_time' => Carbon::now()->addDay()->setHour(11)->toDateTimeString(),
        'end_time' => Carbon::now()->addDay()->setHour(13)->toDateTimeString(),
        'number_of_student' => 25,
        'equipment_needed' => 'Projector',
        'purpose' => 'Conflicting meeting',
    ]);

    $response->assertSessionHasErrors('booking');
});

test('booking creation fails when required fields are missing', function () {
    $this->actingAs($this->regularUser);

    $response = $this->post(route('bookings.store'), [
        'number_of_student' => 25,
        'purpose' => 'Meeting',
    ]);

    $response->assertSessionHasErrors(['room_id', 'start_time', 'end_time']);
});

test('user can edit their own pending booking', function () {
    $this->actingAs($this->regularUser);

    $booking = Booking::factory()->pending()->create(['user_id' => $this->regularUser->id]);

    $response = $this->get(route('bookings.edit', $booking));

    $response->assertStatus(200);
});

test('user cannot edit approved booking', function () {
    $this->actingAs($this->regularUser);

    $booking = Booking::factory()->approved()->create(['user_id' => $this->regularUser->id]);

    $response = $this->get(route('bookings.edit', $booking));

    $response->assertStatus(403);
});

test('user cannot edit another users booking', function () {
    $this->actingAs($this->regularUser);

    $booking = Booking::factory()->create(['user_id' => $this->admin->id]);

    $response = $this->get(route('bookings.edit', $booking));

    $response->assertStatus(403);
});

test('admin can edit any pending booking', function () {
    $this->actingAs($this->admin);

    $booking = Booking::factory()->pending()->create(['user_id' => $this->regularUser->id]);

    $response = $this->get(route('bookings.edit', $booking));

    $response->assertStatus(200);
});

test('user can update their own pending booking', function () {
    $this->actingAs($this->regularUser);

    $booking = Booking::factory()->pending()->create(['user_id' => $this->regularUser->id]);

    $newStart = Carbon::now()->addDays(2)->setHour(14);
    $newEnd = Carbon::now()->addDays(2)->setHour(16);

    $response = $this->put(route('bookings.update', $booking), [
        'room_id' => $booking->room_id,
        'start_time' => $newStart->toDateTimeString(),
        'end_time' => $newEnd->toDateTimeString(),
        'number_of_student' => 30,
        'equipment_needed' => 'Updated equipment',
        'purpose' => 'Updated purpose',
    ]);

    $response->assertRedirect(route('bookings.index'));

    $booking->refresh();
    expect($booking->purpose)->toBe('Updated purpose')
        ->and($booking->number_of_student)->toBe(30);
});

test('user can delete their own booking', function () {
    $this->actingAs($this->regularUser);

    $booking = Booking::factory()->create(['user_id' => $this->regularUser->id]);

    $response = $this->delete(route('bookings.destroy', $booking));

    $response->assertRedirect(route('bookings.index'));

    $this->assertDatabaseMissing('bookings', ['id' => $booking->id]);
});

test('user cannot delete another users booking', function () {
    $this->actingAs($this->regularUser);

    $booking = Booking::factory()->create(['user_id' => $this->admin->id]);

    $response = $this->delete(route('bookings.destroy', $booking));

    $response->assertStatus(403);
});

test('admin can approve booking', function () {
    Mail::fake();

    $this->actingAs($this->admin);

    $booking = Booking::factory()->pending()->create(['user_id' => $this->regularUser->id]);

    $response = $this->post(route('bookings.approve', $booking));

    $response->assertRedirect(route('bookings.index'));

    $booking->refresh();
    expect($booking->status)->toBeTrue();

    Mail::assertQueued(BookingApprovedMail::class);
});

test('regular user cannot approve booking', function () {
    $this->actingAs($this->regularUser);

    $booking = Booking::factory()->pending()->create(['user_id' => $this->admin->id]);

    $response = $this->post(route('bookings.approve', $booking));

    $response->assertStatus(403);
});

test('admin can reject booking with reason', function () {
    Mail::fake();

    $this->actingAs($this->admin);

    $booking = Booking::factory()->pending()->create(['user_id' => $this->regularUser->id]);

    $response = $this->post(route('bookings.reject', $booking), [
        'rejection_reason' => 'Room unavailable',
    ]);

    $response->assertRedirect(route('bookings.index'));

    $booking->refresh();
    expect($booking->status)->toBeFalse()
        ->and($booking->rejection_reason)->toBe('Room unavailable');

    Mail::assertQueued(BookingRejectedMail::class);
});

test('regular user cannot reject booking', function () {
    $this->actingAs($this->regularUser);

    $booking = Booking::factory()->pending()->create(['user_id' => $this->admin->id]);

    $response = $this->post(route('bookings.reject', $booking), [
        'rejection_reason' => 'Some reason',
    ]);

    $response->assertStatus(403);
});

test('can get bookings by room', function () {
    $this->actingAs($this->regularUser);

    $booking1 = Booking::factory()->create(['room_id' => $this->room->id]);
    $booking2 = Booking::factory()->create(['room_id' => $this->room->id]);
    $otherBooking = Booking::factory()->create();

    $response = $this->get('/bookings/room/' . $this->room->id);

    $response->assertStatus(200);

    $bookings = $response->json();
    expect($bookings)->toHaveCount(2);
});

test('can get bookings by room and date', function () {
    $this->actingAs($this->regularUser);

    $targetDate = Carbon::now()->addDay();

    $matchingBooking = Booking::factory()->create([
        'room_id' => $this->room->id,
        'start_time' => $targetDate->copy()->setHour(10),
    ]);

    $differentDateBooking = Booking::factory()->create([
        'room_id' => $this->room->id,
        'start_time' => Carbon::now()->addDays(2)->setHour(10),
    ]);

    $response = $this->get('/bookings/room-and-date/' . $this->room->id . '?date=' . $targetDate->toDateString());

    $response->assertStatus(200);

    $bookings = $response->json();
    expect($bookings)->toHaveCount(1);
});
