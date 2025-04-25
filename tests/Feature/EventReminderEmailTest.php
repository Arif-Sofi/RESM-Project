<?php

namespace Tests\Feature;

use App\Console\Commands\SendEventReminders;
use App\Mail\EventReminderNotification;
use App\Models\Event;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class EventReminderEmailTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function test_event_reminder_sends_email_to_creator_and_staff()
    {
        Mail::fake();

        // Create a user (creator)
        $creator = User::factory()->create();

        // Create staff users
        $staff1 = User::factory()->create();
        $staff2 = User::factory()->create();

        // Set the event start time to be within the 2-minute window
        $startTime = Carbon::now()->addMinutes(60);

        // Create an event
        $event = Event::factory()->create([
            'user_id' => $creator->id,
            'start_at' => $startTime,
            'title' => $this->faker->sentence, // Add a title
        ]);

        // Attach staff to the event
        $event->staff()->attach([$staff1->id, $staff2->id]);

        // Run the SendEventReminders command
        $this->artisan(SendEventReminders::class);

        // Assert that the email was queued to the creator
        Mail::assertQueued(EventReminderNotification::class, function ($mail) use ($creator, $event) {
            return $mail->hasTo($creator->email) &&
                   $mail->event->id === $event->id;
        });

        // Assert that the email was queued to the staff members
        Mail::assertQueued(EventReminderNotification::class, function ($mail) use ($staff1, $event) {
            return $mail->hasTo($staff1->email) &&
                   $mail->event->id === $event->id;
        });

        Mail::assertQueued(EventReminderNotification::class, function ($mail) use ($staff2, $event) {
            return $mail->hasTo($staff2->email) &&
                   $mail->event->id === $event->id;
        });

        // Assert that the reminder_sent_at is not null
        $this->assertNotNull($event->fresh()->reminder_sent_at);
    }
}
