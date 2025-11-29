<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Room;
use App\Models\User;
use App\Services\BookingService;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class BookingSeeder extends Seeder
{
    protected $bookingService;

    protected $faker;

    // Room-specific purposes
    protected $roomPurposes = [
        'Surau' => [
            'Prayer session and religious study',
            'Quran recitation class',
            'Friday prayer preparation',
            'Islamic talk for students',
            'Dhuha prayer session',
            'Religious counseling session',
        ],
        'Makmal Komputer' => [
            'Computer lab practical class',
            'Programming workshop for Form 4',
            'IT literacy training',
            'Digital skills workshop',
            'Online examination session',
            'Coding club activity',
            'Microsoft Office training',
        ],
        'Makmal Sains' => [
            'Science practical session for Form 4 students',
            'Chemistry experiment - Acids and Bases',
            'Physics lab - Electricity experiments',
            'Biology practical - Cell observation',
            'Lab demonstration for Form 3',
            'Science project work session',
            'SPM revision practical',
        ],
        'Makmal RBT' => [
            'RBT practical - Woodwork project',
            'Electronics assembly session',
            'Design and Technology workshop',
            'Technical drawing class',
            'Metalwork practical session',
            'Project-based learning activity',
        ],
        'Bilik Mesyuarat' => [
            'Staff meeting - Weekly briefing',
            'Parent-teacher conference',
            'Department meeting - Curriculum planning',
            'Interview session - New teacher',
            'Training workshop for teachers',
            'PTA committee meeting',
            'School board discussion',
            'Exam committee meeting',
        ],
    ];

    // Rejection reasons
    protected $rejectionReasons = [
        'Room already reserved for school event',
        'Booking conflicts with scheduled maintenance',
        'Insufficient notice period - minimum 24 hours required',
        'Room capacity exceeded for this activity',
        'Required equipment not available on requested date',
        'Booking outside permitted school hours',
        'Another booking with higher priority exists',
        'Room reserved for examination purposes',
    ];

    // Equipment suggestions by room type
    protected $equipmentByRoom = [
        'Surau' => ['Prayer mats', 'Audio system', 'Quran sets', null],
        'Makmal Komputer' => ['All computers', 'Projector and screen', 'Headphones for students', 'Specific software required', null],
        'Makmal Sains' => ['Lab coats and goggles', 'Microscopes', 'Chemical reagents', 'Bunsen burners', null],
        'Makmal RBT' => ['Power tools', 'Safety equipment', 'Raw materials', 'Soldering stations', null],
        'Bilik Mesyuarat' => ['Projector', 'Video conferencing setup', 'Whiteboard markers', null],
    ];

    // Student count ranges by room
    protected $studentRanges = [
        'Surau' => [10, 50],
        'Makmal Komputer' => [15, 30],
        'Makmal Sains' => [20, 40],
        'Makmal RBT' => [15, 35],
        'Bilik Mesyuarat' => [5, 25],
    ];

    // Status weights by time period
    protected $statusWeights = [
        'past' => [
            'approved' => 70,
            'rejected' => 20,
            'pending' => 10,
        ],
        'current' => [
            'approved' => 40,
            'pending' => 40,
            'rejected' => 20,
        ],
        'future' => [
            'pending' => 50,
            'approved' => 40,
            'rejected' => 10,
        ],
    ];

    public function __construct(BookingService $bookingsService)
    {
        $this->bookingService = $bookingsService;
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->faker = Faker::create('en_MY');

        $rooms = Room::all();
        $users = User::all()->filter(fn ($user) => ! $user->isAdmin())->values();

        if ($rooms->isEmpty()) {
            $this->command->info('Warning: No rooms found. Please seed rooms first.');

            return;
        }

        if ($users->isEmpty()) {
            $this->command->info('Warning: No non-admin users found. Please seed users first.');

            return;
        }

        $this->command->info('Generating realistic bookings...');

        // Generate bookings for each time period
        $this->generateBookings($rooms, $users, 'past', 40);
        $this->generateBookings($rooms, $users, 'current', 30);
        $this->generateBookings($rooms, $users, 'future', 40);

        $totalBookings = Booking::count();
        $this->command->info("Successfully created {$totalBookings} bookings.");
    }

    /**
     * Generate bookings for a specific time period.
     */
    protected function generateBookings($rooms, $users, string $period, int $targetCount): void
    {
        $attempts = 0;
        $maxAttempts = $targetCount * 3; // Allow more attempts for clashes
        $created = 0;

        while ($created < $targetCount && $attempts < $maxAttempts) {
            $attempts++;

            $room = $rooms->random();
            $user = $users->random();

            // Get date range based on period
            $date = $this->getDateForPeriod($period);

            // Get realistic school hours time slot
            $timeSlot = $this->getRealisticTimeSlot();
            $startTime = $date->copy()->setTimeFromTimeString($timeSlot['start']);
            $endTime = $date->copy()->setTimeFromTimeString($timeSlot['end']);

            // Check for clashes
            if ($this->bookingService->isClash($room->id, $startTime, $endTime)) {
                continue;
            }

            // Get status based on period
            $status = $this->getStatusForPeriod($period);

            // Get room-appropriate purpose
            $purpose = $this->getPurposeForRoom($room->name);

            // Get appropriate equipment
            $equipment = $this->getEquipmentForRoom($room->name);

            // Get student count based on room capacity
            $studentCount = $this->getStudentCountForRoom($room->name);

            // Create the booking
            $bookingData = [
                'room_id' => $room->id,
                'user_id' => $user->id,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'number_of_student' => $studentCount,
                'equipment_needed' => $equipment,
                'purpose' => $purpose,
                'status' => $status,
                'created_at' => $this->getCreatedAtForPeriod($period, $startTime),
                'updated_at' => now(),
            ];

            // Add rejection reason if rejected
            if ($status === false) {
                $bookingData['rejection_reason'] = Arr::random($this->rejectionReasons);
            }

            Booking::create($bookingData);
            $created++;
        }

        $this->command->info("  - {$period}: Created {$created}/{$targetCount} bookings");
    }

    /**
     * Get a date within the specified period.
     */
    protected function getDateForPeriod(string $period): \Carbon\Carbon
    {
        return match ($period) {
            'past' => now()->subDays($this->faker->numberBetween(1, 30)),
            'current' => now()->addDays($this->faker->numberBetween(-3, 3)),
            'future' => now()->addDays($this->faker->numberBetween(4, 14)),
        };
    }

    /**
     * Get realistic school time slots.
     */
    protected function getRealisticTimeSlot(): array
    {
        $slots = [
            // Morning slots
            ['start' => '07:30', 'end' => '08:30'],
            ['start' => '08:00', 'end' => '09:00'],
            ['start' => '08:30', 'end' => '10:00'],
            ['start' => '09:00', 'end' => '10:30'],
            ['start' => '10:00', 'end' => '11:00'],
            ['start' => '10:30', 'end' => '12:00'],
            ['start' => '11:00', 'end' => '12:30'],
            // Afternoon slots
            ['start' => '12:30', 'end' => '13:30'],
            ['start' => '13:00', 'end' => '14:30'],
            ['start' => '14:00', 'end' => '15:00'],
            ['start' => '14:30', 'end' => '16:00'],
            ['start' => '15:00', 'end' => '16:30'],
            ['start' => '15:30', 'end' => '17:00'],
            // Extended afternoon for meetings/workshops
            ['start' => '14:00', 'end' => '17:00'],
            ['start' => '08:00', 'end' => '11:00'],
        ];

        return Arr::random($slots);
    }

    /**
     * Get weighted random status based on period.
     */
    protected function getStatusForPeriod(string $period): ?bool
    {
        $weights = $this->statusWeights[$period];
        $rand = $this->faker->numberBetween(1, 100);

        if ($rand <= $weights['approved']) {
            return true; // approved
        } elseif ($rand <= $weights['approved'] + $weights['rejected']) {
            return false; // rejected
        } else {
            return null; // pending
        }
    }

    /**
     * Get room-appropriate purpose.
     */
    protected function getPurposeForRoom(string $roomName): string
    {
        // Match room name to purpose category
        foreach ($this->roomPurposes as $key => $purposes) {
            if (str_contains($roomName, $key)) {
                return Arr::random($purposes);
            }
        }

        // Default purposes if no match
        return Arr::random([
            'General meeting',
            'Student activity',
            'Staff training',
            'Special event',
        ]);
    }

    /**
     * Get room-appropriate equipment.
     */
    protected function getEquipmentForRoom(string $roomName): ?string
    {
        foreach ($this->equipmentByRoom as $key => $equipment) {
            if (str_contains($roomName, $key)) {
                return Arr::random($equipment);
            }
        }

        return $this->faker->boolean(50) ? 'Standard AV equipment' : null;
    }

    /**
     * Get appropriate student count for room.
     */
    protected function getStudentCountForRoom(string $roomName): int
    {
        foreach ($this->studentRanges as $key => $range) {
            if (str_contains($roomName, $key)) {
                return $this->faker->numberBetween($range[0], $range[1]);
            }
        }

        return $this->faker->numberBetween(10, 30);
    }

    /**
     * Get realistic created_at timestamp.
     */
    protected function getCreatedAtForPeriod(string $period, $startTime): \Carbon\Carbon
    {
        return match ($period) {
            // Past bookings were created before their start time
            'past' => $startTime->copy()->subDays($this->faker->numberBetween(1, 7)),
            // Current bookings created recently
            'current' => now()->subDays($this->faker->numberBetween(0, 5)),
            // Future bookings created recently
            'future' => now()->subDays($this->faker->numberBetween(0, 3)),
        };
    }
}
