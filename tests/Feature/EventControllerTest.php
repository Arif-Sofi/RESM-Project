<?php

use App\Mail\EventCreatedNotification;
use App\Models\Event;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

beforeEach(function () {
    // Seed roles with specific IDs (isAdmin checks role_id === 1)
    \DB::table('roles')->delete();
    \DB::table('roles')->insert([
        ['id' => 1, 'name' => 'Admin', 'created_at' => now(), 'updated_at' => now()],
        ['id' => 2, 'name' => 'Regular User', 'created_at' => now(), 'updated_at' => now()],
    ]);

    $this->user = User::factory()->create(['role_id' => 2]);
    $this->otherUser = User::factory()->create(['role_id' => 2]);
    $this->staffUser = User::factory()->create(['role_id' => 2]);
});

test('user can view events index page', function () {
    $this->actingAs($this->user);

    $response = $this->get(route('events.index'));

    $response->assertStatus(200)
        ->assertViewIs('events.index')
        ->assertViewHas('users')
        ->assertViewHas('events');
});

test('user sees only their own events and events they are staff on', function () {
    $this->actingAs($this->user);

    // Create an event by the user
    $ownEvent = Event::factory()->create(['user_id' => $this->user->id]);

    // Create an event by another user where user is staff
    $staffEvent = Event::factory()->create(['user_id' => $this->otherUser->id]);
    $staffEvent->staff()->attach($this->user->id);

    // Create an event by another user where user is NOT staff
    $otherEvent = Event::factory()->create(['user_id' => $this->otherUser->id]);

    $response = $this->get(route('events.index'));

    $response->assertStatus(200)
        ->assertViewHas('events', function ($events) use ($ownEvent, $staffEvent, $otherEvent) {
            return $events->contains('id', $ownEvent->id)
                && $events->contains('id', $staffEvent->id)
                && ! $events->contains('id', $otherEvent->id);
        });
});

test('user can create an event', function () {
    Mail::fake();

    $this->actingAs($this->user);

    $startAt = Carbon::now()->addDay()->setHour(10);
    $endAt = Carbon::now()->addDay()->setHour(12);

    $response = $this->post(route('events.store'), [
        'title' => 'Team Meeting',
        'description' => 'Weekly sync meeting',
        'location' => 'Conference Room',
        'start_at' => $startAt->toDateTimeString(),
        'end_at' => $endAt->toDateTimeString(),
        'staff' => [$this->staffUser->id],
    ]);

    $response->assertRedirect(route('events.index'));

    $this->assertDatabaseHas('events', [
        'title' => 'Team Meeting',
        'description' => 'Weekly sync meeting',
        'location' => 'Conference Room',
        'user_id' => $this->user->id,
    ]);

    // Check staff was assigned
    $event = Event::where('title', 'Team Meeting')->first();
    expect($event->staff)->toHaveCount(1);
    expect($event->staff->first()->id)->toBe($this->staffUser->id);

    Mail::assertQueued(EventCreatedNotification::class);
});

test('user can create event via json request', function () {
    Mail::fake();

    $this->actingAs($this->user);

    $startAt = Carbon::now()->addDay()->setHour(10);
    $endAt = Carbon::now()->addDay()->setHour(12);

    $response = $this->postJson(route('events.store'), [
        'title' => 'API Event',
        'description' => 'Created via API',
        'location' => 'Virtual Room',
        'start_at' => $startAt->toDateTimeString(),
        'end_at' => $endAt->toDateTimeString(),
        'staff' => [],
    ]);

    $response->assertStatus(201)
        ->assertJson([
            'success' => true,
            'message' => 'Event created successfully!',
        ]);

    $this->assertDatabaseHas('events', [
        'title' => 'API Event',
        'location' => 'Virtual Room',
        'user_id' => $this->user->id,
    ]);
});

test('event creation requires title', function () {
    $this->actingAs($this->user);

    $response = $this->postJson(route('events.store'), [
        'start_at' => Carbon::now()->addDay()->toDateTimeString(),
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['title']);
});

test('creator can update their event', function () {
    $this->actingAs($this->user);

    $event = Event::factory()->create(['user_id' => $this->user->id]);

    $response = $this->patchJson(route('events.update', $event), [
        'title' => 'Updated Title',
        'description' => 'Updated description',
        'location' => 'Updated Location',
        'start_at' => $event->start_at->toDateTimeString(),
        'end_at' => $event->end_at?->toDateTimeString(),
        'staff' => [$this->staffUser->id],
    ]);

    $response->assertOk()
        ->assertJson([
            'success' => true,
            'message' => 'Event updated successfully!',
        ]);

    $this->assertDatabaseHas('events', [
        'id' => $event->id,
        'title' => 'Updated Title',
        'description' => 'Updated description',
        'location' => 'Updated Location',
    ]);
});

test('non-creator cannot update event', function () {
    $this->actingAs($this->otherUser);

    $event = Event::factory()->create(['user_id' => $this->user->id]);

    $response = $this->patchJson(route('events.update', $event), [
        'title' => 'Hacked Title',
        'start_at' => $event->start_at->toDateTimeString(),
    ]);

    $response->assertForbidden();

    $this->assertDatabaseMissing('events', [
        'id' => $event->id,
        'title' => 'Hacked Title',
    ]);
});

test('creator can delete their event', function () {
    $this->actingAs($this->user);

    $event = Event::factory()->create(['user_id' => $this->user->id]);

    $response = $this->deleteJson(route('events.destroy', $event));

    $response->assertOk()
        ->assertJson([
            'success' => true,
            'message' => 'Event deleted successfully!',
        ]);

    $this->assertDatabaseMissing('events', ['id' => $event->id]);
});

test('non-creator cannot delete event', function () {
    $this->actingAs($this->otherUser);

    $event = Event::factory()->create(['user_id' => $this->user->id]);

    $response = $this->deleteJson(route('events.destroy', $event));

    $response->assertForbidden();

    $this->assertDatabaseHas('events', ['id' => $event->id]);
});

test('api events endpoint returns json', function () {
    $this->actingAs($this->user);

    $event = Event::factory()->create(['user_id' => $this->user->id]);

    $response = $this->getJson(route('api.events'));

    $response->assertOk()
        ->assertJsonStructure([
            '*' => ['id', 'title', 'start_at', 'end_at', 'user_id'],
        ]);
});

test('api events filters by user access', function () {
    $this->actingAs($this->user);

    // User's own event
    $ownEvent = Event::factory()->create(['user_id' => $this->user->id]);

    // Event where user is staff
    $staffEvent = Event::factory()->create(['user_id' => $this->otherUser->id]);
    $staffEvent->staff()->attach($this->user->id);

    // Event user has no access to
    $otherEvent = Event::factory()->create(['user_id' => $this->otherUser->id]);

    $response = $this->getJson(route('api.events'));

    $response->assertOk();
    $eventIds = collect($response->json())->pluck('id');

    expect($eventIds)->toContain($ownEvent->id);
    expect($eventIds)->toContain($staffEvent->id);
    expect($eventIds)->not->toContain($otherEvent->id);
});
