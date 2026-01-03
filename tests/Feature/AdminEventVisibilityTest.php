<?php

use App\Models\Event;
use App\Models\User;

beforeEach(function () {
    // Seed roles
    \DB::table('roles')->delete();
    \DB::table('roles')->insert([
        ['id' => 1, 'name' => 'Admin', 'created_at' => now(), 'updated_at' => now()],
        ['id' => 2, 'name' => 'Regular User', 'created_at' => now(), 'updated_at' => now()],
    ]);
});

test('admin sees all events in index', function () {
    $admin = User::factory()->create(['role_id' => 1]);
    $user1 = User::factory()->create(['role_id' => 2]);
    $user2 = User::factory()->create(['role_id' => 2]);

    $event1 = Event::factory()->create(['user_id' => $user1->id]);
    $event2 = Event::factory()->create(['user_id' => $user2->id]);

    $this->actingAs($admin)
        ->get(route('events.index'))
        ->assertOk()
        ->assertViewHas('events', function ($events) use ($event1, $event2) {
            return $events->contains('id', $event1->id)
                && $events->contains('id', $event2->id);
        });
});

test('admin sees all events in api', function () {
    $admin = User::factory()->create(['role_id' => 1]);
    $user1 = User::factory()->create(['role_id' => 2]);
    $user2 = User::factory()->create(['role_id' => 2]);

    $event1 = Event::factory()->create(['user_id' => $user1->id]);
    $event2 = Event::factory()->create(['user_id' => $user2->id]);

    $this->actingAs($admin)
        ->getJson(route('api.events'))
        ->assertOk()
        ->assertJsonFragment(['id' => $event1->id])
        ->assertJsonFragment(['id' => $event2->id]);
});

test('regular user only sees own events', function () {
    $user1 = User::factory()->create(['role_id' => 2]);
    $user2 = User::factory()->create(['role_id' => 2]);

    $event1 = Event::factory()->create(['user_id' => $user1->id]);
    $event2 = Event::factory()->create(['user_id' => $user2->id]);

    $this->actingAs($user1)
        ->get(route('events.index'))
        ->assertOk()
        ->assertViewHas('events', function ($events) use ($event1, $event2) {
            return $events->contains('id', $event1->id)
                && ! $events->contains('id', $event2->id);
        });

    $this->actingAs($user1)
        ->getJson(route('api.events'))
        ->assertOk()
        ->assertJsonFragment(['id' => $event1->id])
        ->assertJsonMissing(['id' => $event2->id]);
});
