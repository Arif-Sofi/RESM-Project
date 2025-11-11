<?php

use App\Models\Booking;
use App\Models\Event;
use App\Models\Role;
use App\Models\User;

test('isAdmin returns true for admin users', function () {
    $user = User::factory()->create(['role_id' => 1]);

    expect($user->isAdmin())->toBeTrue();
});

test('isAdmin returns false for non-admin users', function () {
    $user = User::factory()->create(['role_id' => 2]);

    expect($user->isAdmin())->toBeFalse();
});

test('user has many bookings relationship', function () {
    $user = User::factory()->create();
    $booking = Booking::factory()->create(['user_id' => $user->id]);

    expect($user->bookings)->toHaveCount(1)
        ->and($user->bookings->first()->id)->toBe($booking->id);
});

test('user has many events relationship', function () {
    $user = User::factory()->create();
    $event = Event::factory()->create(['user_id' => $user->id]);

    expect($user->events)->toHaveCount(1)
        ->and($user->events->first()->id)->toBe($event->id);
});

test('user belongs to role relationship', function () {
    $user = User::factory()->create(['role_id' => 1]);

    expect($user->role)->toBeInstanceOf(Role::class)
        ->and($user->role->id)->toBe(1);
});
