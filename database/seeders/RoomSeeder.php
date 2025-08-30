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

        Room::create([
            'name' => 'Surau',
            'description' => '1 Whiteboard, 4 Speakers, 2 Microphones',
            'location_details' => 'Ground Floor',
        ]);

        Room::create([
            'name' => 'Makmal Komputer',
            'description' => '15 Desktop Computers, 1 Projector, 1 Whiteboard',
            'location_details' => '2nd Floor',
        ]);

        Room::create([
            'name' => 'Makmal Sains',
            'description' => '8 Lab Stations, 40 Lab Stools, Safety Equipment',
            'location_details' => '1st Floor',
        ]);

        Room::create([
            'name' => 'Makmal RBT',
            'description' => '8 Lab Stations, 40 Lab Stools, Safety Equipment, 50 Lab Goggles',
            'location_details' => 'Ground Floor',
        ]);

        Room::create([
            'name' => 'Bilik Mesyuarat 1',
            'description' => '20 Chairs, 1 Conference Table, 1 Projector',
            'location_details' => 'Ground Floor',
        ]);

        Room::create([
            'name' => 'Bilik Mesyuarat 2',
            'description' => '20 Chairs, 1 Conference Table, 1 Projector',
            'location_details' => '1st Floor',
        ]);
    }
}
