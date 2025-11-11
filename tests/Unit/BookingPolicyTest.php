<?php

use App\Models\Booking;
use App\Models\User;
use App\Policies\BookingPolicy;

beforeEach(function () {
    $this->admin = User::factory()->create(['role_id' => 1]);
    $this->regularUser = User::factory()->create(['role_id' => 2]);
    $this->policy = new BookingPolicy();
});

test('admin can view any booking', function () {
    $booking = Booking::factory()->create();

    expect($this->policy->viewAny($this->admin))->toBeTrue();
});

test('regular user can view any booking', function () {
    $booking = Booking::factory()->create();

    expect($this->policy->viewAny($this->regularUser))->toBeTrue();
});

test('admin can view specific booking', function () {
    $booking = Booking::factory()->create();

    expect($this->policy->view($this->admin, $booking))->toBeTrue();
});

test('regular user can view their own booking', function () {
    $booking = Booking::factory()->create(['user_id' => $this->regularUser->id]);

    expect($this->policy->view($this->regularUser, $booking))->toBeTrue();
});

test('regular user cannot view another users booking', function () {
    $booking = Booking::factory()->create(['user_id' => $this->admin->id]);

    expect($this->policy->view($this->regularUser, $booking))->toBeFalse();
});

test('admin can update any pending booking', function () {
    $booking = Booking::factory()->pending()->create();

    expect($this->policy->update($this->admin, $booking))->toBeTrue();
});

test('admin can update any approved booking', function () {
    $booking = Booking::factory()->approved()->create();

    expect($this->policy->update($this->admin, $booking))->toBeTrue();
});

test('regular user can update their own pending booking', function () {
    $booking = Booking::factory()->pending()->create(['user_id' => $this->regularUser->id]);

    expect($this->policy->update($this->regularUser, $booking))->toBeTrue();
});

test('regular user cannot update their approved booking', function () {
    $booking = Booking::factory()->approved()->create(['user_id' => $this->regularUser->id]);

    expect($this->policy->update($this->regularUser, $booking))->toBeFalse();
});

test('regular user cannot update their rejected booking', function () {
    $booking = Booking::factory()->rejected()->create(['user_id' => $this->regularUser->id]);

    expect($this->policy->update($this->regularUser, $booking))->toBeFalse();
});

test('regular user cannot update another users booking', function () {
    $booking = Booking::factory()->pending()->create(['user_id' => $this->admin->id]);

    expect($this->policy->update($this->regularUser, $booking))->toBeFalse();
});

test('admin can delete any booking', function () {
    $booking = Booking::factory()->create();

    expect($this->policy->delete($this->admin, $booking))->toBeTrue();
});

test('regular user can delete their own booking', function () {
    $booking = Booking::factory()->create(['user_id' => $this->regularUser->id]);

    expect($this->policy->delete($this->regularUser, $booking))->toBeTrue();
});

test('regular user cannot delete another users booking', function () {
    $booking = Booking::factory()->create(['user_id' => $this->admin->id]);

    expect($this->policy->delete($this->regularUser, $booking))->toBeFalse();
});
