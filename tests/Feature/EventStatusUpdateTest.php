<?php

use App\Models\Event;
use Carbon\Carbon;

beforeEach(function () {
    // Seed roles
    \DB::table('roles')->delete();
    \DB::table('roles')->insert([
        ['id' => 1, 'name' => 'Admin', 'created_at' => now(), 'updated_at' => now()],
        ['id' => 2, 'name' => 'Regular User', 'created_at' => now(), 'updated_at' => now()],
    ]);
});

test('command updates past event status to COMPLETED', function () {
    // Create a past event
    $pastEvent = Event::factory()->create([
        'end_at' => Carbon::now()->subHour(),
        'status' => 'NOT-COMPLETED',
    ]);

    // Create a future event
    $futureEvent = Event::factory()->create([
        'end_at' => Carbon::now()->addHour(),
        'status' => 'NOT-COMPLETED',
    ]);

    // Run the command
    $this->artisan('events:update-status')
        ->assertExitCode(0);

    // Assert past event is updated
    expect($pastEvent->fresh()->status)->toBe('COMPLETED');

    // Assert future event is NOT updated
    expect($futureEvent->fresh()->status)->toBe('NOT-COMPLETED');
});
