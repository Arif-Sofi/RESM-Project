<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class EventSeeder extends Seeder
{
    /**
     * Sample event titles by category.
     */
    protected array $eventTitles = [
        'meeting' => [
            'Staff Meeting',
            'Department Meeting',
            'Weekly Standup',
            'Project Review',
            'Budget Planning',
            'Team Building Session',
        ],
        'training' => [
            'New Software Training',
            'Safety Training',
            'Professional Development Workshop',
            'First Aid Training',
            'IT Security Training',
        ],
        'celebration' => [
            'Birthday Celebration',
            'Farewell Party',
            'Welcome New Staff',
            'Achievement Celebration',
            'Holiday Party',
        ],
        'academic' => [
            'Parent-Teacher Conference',
            'Student Exhibition',
            'Science Fair',
            'Art Show',
            'Career Day',
        ],
    ];

    /**
     * Sample descriptions.
     */
    protected array $descriptions = [
        'Please arrive 10 minutes early.',
        'Refreshments will be provided.',
        'All staff are encouraged to attend.',
        'Bring your laptop if possible.',
        'Dress code: Business casual.',
        'Attendance is mandatory.',
        null,
        null,
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();

        if ($users->isEmpty()) {
            $this->command->warn('No users found. Please run UserSeeder first.');

            return;
        }

        $this->command->info('Creating sample events...');

        // Create 25 sample events
        $eventsCreated = 0;

        for ($i = 0; $i < 25; $i++) {
            $creator = $users->random();
            $category = Arr::random(array_keys($this->eventTitles));
            $title = Arr::random($this->eventTitles[$category]);
            $description = Arr::random($this->descriptions);

            // Determine date range: past, current, or future
            $period = Arr::random(['past', 'past', 'current', 'future', 'future', 'future']);
            $date = $this->getDateForPeriod($period);
            $timeSlot = $this->getRandomTimeSlot();

            $startAt = Carbon::parse($date)->setTimeFromTimeString($timeSlot['start']);
            $endAt = Carbon::parse($date)->setTimeFromTimeString($timeSlot['end']);

            // Create the event
            $event = Event::create([
                'title' => $title,
                'description' => $description,
                'start_at' => $startAt,
                'end_at' => $endAt,
                'user_id' => $creator->id,
            ]);

            // Assign random staff (0-4 staff members)
            $staffCount = rand(0, min(4, $users->count() - 1));
            if ($staffCount > 0) {
                $potentialStaff = $users->where('id', '!=', $creator->id);
                $staff = $potentialStaff->random(min($staffCount, $potentialStaff->count()));
                $event->staff()->sync($staff->pluck('id'));
            }

            $eventsCreated++;
        }

        $this->command->info("Created {$eventsCreated} sample events.");
    }

    /**
     * Get a date based on the period.
     */
    private function getDateForPeriod(string $period): string
    {
        return match ($period) {
            'past' => Carbon::now()->subDays(rand(1, 30))->toDateString(),
            'current' => Carbon::now()->addDays(rand(0, 3))->toDateString(),
            'future' => Carbon::now()->addDays(rand(4, 30))->toDateString(),
        };
    }

    /**
     * Get a random time slot for events.
     */
    private function getRandomTimeSlot(): array
    {
        $timeSlots = [
            ['start' => '08:00', 'end' => '09:00'],
            ['start' => '09:00', 'end' => '10:00'],
            ['start' => '10:00', 'end' => '11:00'],
            ['start' => '10:00', 'end' => '12:00'],
            ['start' => '13:00', 'end' => '14:00'],
            ['start' => '14:00', 'end' => '15:00'],
            ['start' => '14:00', 'end' => '16:00'],
            ['start' => '15:00', 'end' => '17:00'],
            ['start' => '09:00', 'end' => '12:00'],
            ['start' => '13:00', 'end' => '17:00'],
        ];

        return Arr::random($timeSlots);
    }
}
