<?php

namespace Database\Seeders;

use App\Models\Room;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

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
