<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Room;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Room::create([
            'name' => 'Room 101',
            'description' => '小規模な会議室。プロジェクター完備。',
            'location_details' => '本館1階',
        ]);

        Room::create([
            'name' => 'Room 205',
            'description' => '中規模セミナー室。ホワイトボード、音響設備あり。',
            'location_details' => '本館2階',
        ]);

        Room::create([
            'name' => 'Hall A',
            'description' => '大規模イベントホール。ステージ、照明設備あり。',
            'location_details' => '別館',
        ]);
    }
}
