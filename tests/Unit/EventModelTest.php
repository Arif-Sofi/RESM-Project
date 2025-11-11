<?php

use App\Models\Event;
use App\Models\User;

test('event belongs to creator relationship', function () {
    $user = User::factory()->create();
    $event = Event::factory()->create(['user_id' => $user->id]);

    expect($event->creator)->toBeInstanceOf(User::class)
        ->and($event->creator->id)->toBe($user->id);
});

test('event has many staff relationship', function () {
    $event = Event::factory()->create();
    $staff1 = User::factory()->create();
    $staff2 = User::factory()->create();

    $event->staff()->attach([$staff1->id, $staff2->id]);

    expect($event->staff)->toHaveCount(2)
        ->and($event->staff->pluck('id')->toArray())->toContain($staff1->id, $staff2->id);
});

test('event dates are cast to datetime', function () {
    $event = Event::factory()->create();

    expect($event->start_at)->toBeInstanceOf(Carbon\Carbon::class)
        ->and($event->end_at)->toBeInstanceOf(Carbon\Carbon::class);
});
