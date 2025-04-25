<?php

namespace Tests\Feature;

use App\Mail\EventCreatedNotification;
use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class EventCreationEmailTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function test_event_creation_sends_email_to_creator_and_staff()
    {
        Mail::fake();

        // Create a user (creator)
        $creator = User::factory()->create();

        // Create staff users
        $staff1 = User::factory()->create();
        $staff2 = User::factory()->create();

        // Event data
        $eventData = [
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'start_at' => $this->faker->dateTimeBetween('now', '+1 week')->format('Y-m-d H:i:s'),
            'end_at' => $this->faker->dateTimeBetween('+1 week', '+2 weeks')->format('Y-m-d H:i:s'),
            'staff' => [$staff1->id, $staff2->id],
            'user_id' => $creator->id,
        ];

        // Act as the creator user
        $this->actingAs($creator);

        // Post to the store route
        $response = $this->postJson(route('events.store'), $eventData);

        // Assert that the event was created
        $response->assertStatus(201);

        // Assert that the email was queued to the creator
        Mail::assertQueued(EventCreatedNotification::class, function ($mail) use ($creator, $eventData) {
            return $mail->hasTo($creator->email) &&
                   $mail->event->title === $eventData['title'];
        });

        // Assert that the email was queued to the staff members
        Mail::assertQueued(EventCreatedNotification::class, function ($mail) use ($staff1, $eventData) {
            return $mail->hasTo($staff1->email) &&
                   $mail->event->title === $eventData['title'];
        });

        Mail::assertQueued(EventCreatedNotification::class, function ($mail) use ($staff2, $eventData) {
            return $mail->hasTo($staff2->email) &&
                   $mail->event->title === $eventData['title'];
        });
    }
}
