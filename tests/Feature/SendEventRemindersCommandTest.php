<?php

use App\Mail\EventReminderNotification;
use App\Models\Event;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

beforeEach(function () {
    // Seed roles
    \DB::table('roles')->delete();
    \DB::table('roles')->insert([
        ['id' => 1, 'name' => 'Admin', 'created_at' => now(), 'updated_at' => now()],
        ['id' => 2, 'name' => 'Regular User', 'created_at' => now(), 'updated_at' => now()],
    ]);

    $this->creator = User::factory()->create(['role_id' => 2]);
    $this->staff1 = User::factory()->create(['role_id' => 2]);
    $this->staff2 = User::factory()->create(['role_id' => 2]);
});

test('command sends reminders for events starting in about one hour', function () {
    Mail::fake();

    // Event starting in exactly 60 minutes (within 59-61 minute window)
    $eventSoon = Event::factory()->create([
        'user_id' => $this->creator->id,
        'start_at' => Carbon::now()->addMinutes(60),
        'reminder_sent_at' => null,
    ]);
    $eventSoon->staff()->attach([$this->staff1->id, $this->staff2->id]);

    $this->artisan('app:send-event-reminders')
        ->assertExitCode(0);

    // Should send to creator and 2 staff members = 3 emails
    Mail::assertQueued(EventReminderNotification::class, 3);

    // Event should be marked as reminder sent
    $eventSoon->refresh();
    expect($eventSoon->reminder_sent_at)->not->toBeNull();
});

test('command does not send reminders for events already reminded', function () {
    Mail::fake();

    // Event starting soon but already reminded
    Event::factory()->create([
        'user_id' => $this->creator->id,
        'start_at' => Carbon::now()->addMinutes(30),
        'reminder_sent_at' => Carbon::now()->subMinutes(30),
    ]);

    $this->artisan('app:send-event-reminders')
        ->assertExitCode(0);

    Mail::assertNotQueued(EventReminderNotification::class);
});

test('command does not send reminders for events starting more than an hour away', function () {
    Mail::fake();

    // Event starting in 2 hours (should NOT receive reminder)
    Event::factory()->create([
        'user_id' => $this->creator->id,
        'start_at' => Carbon::now()->addHours(2),
        'reminder_sent_at' => null,
    ]);

    $this->artisan('app:send-event-reminders')
        ->assertExitCode(0);

    Mail::assertNotQueued(EventReminderNotification::class);
});

test('command does not send reminders for past events', function () {
    Mail::fake();

    // Event that already started (should NOT receive reminder)
    Event::factory()->create([
        'user_id' => $this->creator->id,
        'start_at' => Carbon::now()->subMinutes(10),
        'reminder_sent_at' => null,
    ]);

    $this->artisan('app:send-event-reminders')
        ->assertExitCode(0);

    Mail::assertNotQueued(EventReminderNotification::class);
});

test('command sends reminders to both creator and staff', function () {
    Mail::fake();

    // Event starting in exactly 60 minutes (within 59-61 minute window)
    $event = Event::factory()->create([
        'user_id' => $this->creator->id,
        'start_at' => Carbon::now()->addMinutes(60),
        'reminder_sent_at' => null,
    ]);
    $event->staff()->attach($this->staff1->id);

    $this->artisan('app:send-event-reminders')
        ->assertExitCode(0);

    // Should send to creator + 1 staff = 2 emails
    Mail::assertQueued(EventReminderNotification::class, 2);

    Mail::assertQueued(EventReminderNotification::class, function ($mail) {
        return $mail->hasTo($this->creator->email) || $mail->hasTo($this->staff1->email);
    });
});

test('command handles events with no staff', function () {
    Mail::fake();

    // Event starting in exactly 60 minutes (within 59-61 minute window)
    Event::factory()->create([
        'user_id' => $this->creator->id,
        'start_at' => Carbon::now()->addMinutes(60),
        'reminder_sent_at' => null,
    ]);

    $this->artisan('app:send-event-reminders')
        ->assertExitCode(0);

    // Should send only to creator = 1 email
    Mail::assertQueued(EventReminderNotification::class, 1);
});

test('command outputs success message when sending reminders', function () {
    Mail::fake();

    // Event starting in exactly 60 minutes (within 59-61 minute window)
    Event::factory()->create([
        'user_id' => $this->creator->id,
        'title' => 'Test Meeting',
        'start_at' => Carbon::now()->addMinutes(60),
        'reminder_sent_at' => null,
    ]);

    $this->artisan('app:send-event-reminders')
        ->expectsOutput('Sent reminder for event: Test Meeting')
        ->expectsOutput('Event reminders sent successfully.')
        ->assertExitCode(0);
});

test('command outputs success message when no events need reminders', function () {
    Mail::fake();

    $this->artisan('app:send-event-reminders')
        ->expectsOutput('Event reminders sent successfully.')
        ->assertExitCode(0);
});
