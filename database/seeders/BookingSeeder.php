<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr; // For Arr::random
use Faker\Factory as Faker; // For Faker

class BookingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('ja_JP');
        $roomIds = DB::table('rooms')->pluck('id')->toArray();
        $userIds = DB::table('users')->pluck('id')->toArray();

        if (empty($roomIds)) {
            $this->command->info('Warning: No rooms found. Please seed rooms first.');
            return;
        }
        if (empty($userIds)) {
            $this->command->info('Warning: No users found. Please seed users first.');
            return;
        }

        $statuses = [0, 1, null];
        for ($i = 0; $i < 100; $i++) {
            // 今から最大2週間以内の日付で、午前9時から午後5時の間に設定
            $start_time = now()
                ->addDays($faker->numberBetween(0, 14))
                ->setHour($faker->numberBetween(9, 17))
                ->setMinute($faker->randomElement([0, 15, 30, 45])); // 15分刻みなど、より現実的に

            $end_time = $start_time->copy()->addHours($faker->numberBetween(1, 3))
                                      ->addMinutes($faker->randomElement([0, 30]));

            if ($end_time->lessThanOrEqualTo($start_time)) {
                $end_time = $start_time->copy()->addHour();
            }

            DB::table('bookings')->insert([
                'room_id' => Arr::random($roomIds),
                'user_id' => Arr::random($userIds),
                'start_time' => $start_time,
                'end_time' => $end_time,
                'number_of_student' => $faker->numberBetween(1, 30),
                'equipment_needed' => $faker->boolean(50) ? $faker->sentence(3) : null,
                'purpose' => $faker->randomElement([
                    'Team Meeting',
                    'Client Presentation',
                    'Workshop Session',
                    'Lecture',
                    'Study Group',
                    'Interview',
                    'Private Study'
                ]),
                'status' => $faker->randomElement($statuses),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
