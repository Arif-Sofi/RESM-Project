<?php

use App\Models\Room;
use App\Models\User;

beforeEach(function () {
    $this->admin = User::factory()->create(['role_id' => 1]);
    $this->regularUser = User::factory()->create(['role_id' => 2]);
});

test('admin can view all rooms', function () {
    $this->actingAs($this->admin);

    $room1 = Room::factory()->create();
    $room2 = Room::factory()->create();

    $response = $this->get(route('rooms.index'));

    $response->assertStatus(200)
        ->assertSee($room1->name)
        ->assertSee($room2->name);
});

test('regular user can view all rooms', function () {
    $this->actingAs($this->regularUser);

    $room1 = Room::factory()->create();
    $room2 = Room::factory()->create();

    $response = $this->get(route('rooms.index'));

    $response->assertStatus(200)
        ->assertSee($room1->name)
        ->assertSee($room2->name);
});

test('admin can create a room', function () {
    $this->actingAs($this->admin);

    $response = $this->post(route('rooms.store'), [
        'name' => 'Conference Room A',
        'description' => 'Large conference room',
        'location_details' => 'Building 1, Floor 3',
    ]);

    $response->assertRedirect(route('rooms.index'));

    $this->assertDatabaseHas('rooms', [
        'name' => 'Conference Room A',
    ]);
});

test('regular user cannot create a room', function () {
    $this->actingAs($this->regularUser);

    $response = $this->post(route('rooms.store'), [
        'name' => 'Conference Room B',
        'description' => 'Small conference room',
        'location_details' => 'Building 2, Floor 1',
    ]);

    $response->assertStatus(403);
});

test('admin can update a room', function () {
    $this->actingAs($this->admin);

    $room = Room::factory()->create();

    $response = $this->put(route('rooms.update', $room), [
        'name' => 'Updated Room Name',
        'description' => 'Updated description',
        'location_details' => 'Updated location',
    ]);

    $response->assertRedirect(route('rooms.index'));

    $room->refresh();
    expect($room->name)->toBe('Updated Room Name');
});

test('regular user cannot update a room', function () {
    $this->actingAs($this->regularUser);

    $room = Room::factory()->create();

    $response = $this->put(route('rooms.update', $room), [
        'name' => 'Updated Room Name',
        'description' => 'Updated description',
        'location_details' => 'Updated location',
    ]);

    $response->assertStatus(403);
});

test('admin can delete a room', function () {
    $this->actingAs($this->admin);

    $room = Room::factory()->create();

    $response = $this->delete(route('rooms.destroy', $room));

    $response->assertRedirect(route('rooms.index'));

    $this->assertDatabaseMissing('rooms', ['id' => $room->id]);
});

test('regular user cannot delete a room', function () {
    $this->actingAs($this->regularUser);

    $room = Room::factory()->create();

    $response = $this->delete(route('rooms.destroy', $room));

    $response->assertStatus(403);
});
