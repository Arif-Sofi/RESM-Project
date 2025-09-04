<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Faker\Factory as Faker;
use App\Models\Booking;
use App\Models\Room;
use App\Models\User;
use App\Services\BookingService;

class BookingSeeder extends Seeder
{
    protected $bookingService;

    public function __construct(BookingService $bookingsService)
    {
        $this->bookingService = $bookingsService;
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('ja_JP');

        $roomIds = Room::pluck('id')->toArray();
        $userIds = User::pluck('id')->toArray();
        $userIds = array_filter($userIds, function ($id) {
            $user = User::find($id);
            return !$user->isAdmin();
        });


        if (empty($roomIds)) {
            $this->command->info('Warning: No rooms found. Please seed rooms first.');
            return;
        }
        if (empty($userIds)) {
            $this->command->info('Warning: No users found. Please seed users first.');
            return;
        }

        $statuses = [0, 1, null];
        $attempts = 0; // 無限ループを避けるための試行回数カウンター・Clashを確認する際に使用
        $maxAttempts = 50;
        $numberOfBookingsToGenerate = 30;

        for ($i = 0; $i < $numberOfBookingsToGenerate; ) {
            if ($attempts >= $maxAttempts) {
                $this->command->info('Warning: Could not generate all desired bookings due to too many clashes.');
                break;
            }

            $roomId = Arr::random($roomIds);

            // 今から最大2週間以内の日付で、午前9時から午後5時の間に設定
            $start_time = now()
                ->addDays($faker->numberBetween(0, 14))
                ->setHour($faker->numberBetween(9, 17))
                ->setMinute($faker->randomElement([0, 15, 30, 45])); // 15分刻みなど、より現実的に

            $end_time = $start_time
                ->copy()
                ->addHours($faker->numberBetween(1, 3))
                ->addMinutes($faker->randomElement([0, 30]));

            // 終了時刻が開始時刻と同じかそれより前にならないようにする
            if ($end_time->lessThanOrEqualTo($start_time)) {
                $end_time = $start_time->copy()->addHour();
            }

            // 新しい予約が既存の予約と競合しないかチェック
            if (!$this->bookingService->isClash($roomId, $start_time, $end_time)) {
                Booking::create([
                    'room_id' => $roomId,
                    'user_id' => Arr::random($userIds),
                    'start_time' => $start_time,
                    'end_time' => $end_time,
                    'number_of_student' => $faker->numberBetween(1, 30),
                    'equipment_needed' => $faker->boolean(50) ? $faker->sentence(3) : null,
                    'purpose' => $faker->randomElement(['Team Meeting', 'Client Presentation', 'Workshop Session', 'Lecture', 'Study Group', 'Interview', 'Private Study']),
                    'status' => $faker->randomElement($statuses),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $i++;
                $attempts = 0;
            } else {
                $attempts++; // 競合した場合は試行回数を増やす
                // $this->command->info("Clash detected for room {$roomId} at {$start_time}. Retrying..."); // デバッグ用
            }
        }
    }
}
