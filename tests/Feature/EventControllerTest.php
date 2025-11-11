<?php

use App\Models\Event;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\EventCreatedNotification;

beforeEach(function () {
    $this->user = User::factory()->create();
});

test('user can view their events and events they are staff on', function () {
    $this->actingAs($this->user);

    $theirEvent = Event::factory()->create(['user_id' => $this->user->id]);

    $staffEvent = Event::factory()->create();
    $staffEvent->staff()->attach($this->user->id);

    $otherEvent = Event::factory()->create();

    $response = $this->get(route('events.index'));

    $response->assertStatus(200)
        ->assertSee($theirEvent->title)
        ->assertSee($staffEvent->title)
        ->assertDontSee($otherEvent->title);
});

test('authenticated user can create an event', function () {
    Mail::fake();

    $this->actingAs($this->user);

    $response = $this->post(route('events.store'), [
        'title' => 'Team Meeting',
        'description' => 'Quarterly planning',
        'start_at' => Carbon::now()->addDay()->toDateTimeString(),
        'end_at' => Carbon::now()->addDay()->addHours(2)->toDateTimeString(),
    ]);

    $response->assertRedirect(route('events.index'));

    $this->assertDatabaseHas('events', [
        'title' => 'Team Meeting',
        'user_id' => $this->user->id,
    ]);

    Mail::assertSent(EventCreatedNotification::class);
});

test('user can create event with staff members', function () {
    $this->actingAs($this->user);

    $staff1 = User::factory()->create();
    $staff2 = User::factory()->create();

    $response = $this->post(route('events.store'), [
        'title' => 'Team Meeting',
        'description' => 'Quarterly planning',
        'start_at' => Carbon::now()->addDay()->toDateTimeString(),
        'end_at' => Carbon::now()->addDay()->addHours(2)->toDateTimeString(),
        'staff' => [$staff1->id, $staff2->id],
    ]);

    $response->assertRedirect(route('events.index'));

    $event = Event::where('title', 'Team Meeting')->first();
    expect($event->staff)->toHaveCount(2)
        ->and($event->staff->pluck('id')->toArray())->toContain($staff1->id, $staff2->id);
});

test('user can update their own event', function () {
    $this->actingAs($this->user);

    $event = Event::factory()->create(['user_id' => $this->user->id]);

    $response = $this->put(route('events.update', $event), [
        'title' => 'Updated Title',
        'description' => 'Updated description',
        'start_at' => Carbon::now()->addDays(2)->toDateTimeString(),
        'end_at' => Carbon::now()->addDays(2)->addHours(3)->toDateTimeString(),
    ]);

    $response->assertRedirect(route('events.index'));

    $event->refresh();
    expect($event->title)->toBe('Updated Title')
        ->and($event->description)->toBe('Updated description');
});

test('user cannot update another users event', function () {
    $this->actingAs($this->user);

    $otherUser = User::factory()->create();
    $event = Event::factory()->create(['user_id' => $otherUser->id]);

    $response = $this->put(route('events.update', $event), [
        'title' => 'Updated Title',
        'description' => 'Updated description',
        'start_at' => Carbon::now()->addDays(2)->toDateTimeString(),
        'end_at' => Carbon::now()->addDays(2)->addHours(3)->toDateTimeString(),
    ]);

    $response->assertStatus(403);
});

test('user can delete their own event', function () {
    $this->actingAs($this->user);

    $event = Event::factory()->create(['user_id' => $this->user->id]);

    $response = $this->delete(route('events.destroy', $event));

    $response->assertRedirect(route('events.index'));

    $this->assertDatabaseMissing('events', ['id' => $event->id]);
});

test('user cannot delete another users event', function () {
    $this->actingAs($this->user);

    $otherUser = User::factory()->create();
    $event = Event::factory()->create(['user_id' => $otherUser->id]);

    $response = $this->delete(route('events.destroy', $event));

    $response->assertStatus(403);
});

test('api endpoint returns events in json format', function () {
    $this->actingAs($this->user);

    $event1 = Event::factory()->create(['user_id' => $this->user->id]);
    $event2 = Event::factory()->create(['user_id' => $this->user->id]);

    $response = $this->get('/api/events');

    $response->assertStatus(200)
        ->assertJsonCount(2);
});
