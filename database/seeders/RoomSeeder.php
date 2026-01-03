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
            'name' => 'Makmal Komputer 1',
            'description' => '15 Desktop Computers, 1 Projector, 1 Whiteboard',
            'location_details' => '1st Floor',
        ]);

        Room::create([
            'name' => 'Makmal Komputer 2',
            'description' => '15 Desktop Computers, 1 Projector, 1 Whiteboard',
            'location_details' => '2nd Floor',
        ]);

        Room::create([
            'name' => 'Bilik Sains 1',
            'description' => '8 Lab Stations, 40 Lab Stools, Safety Equipment',
            'location_details' => '1st Floor',
        ]);

        Room::create([
            'name' => 'Bilik Sains 2',
            'description' => '8 Lab Stations, 40 Lab Stools, Safety Equipment',
            'location_details' => '2nd Floor',
        ]);

        Room::create([
            'name' => 'Bengkel RBT',
            'description' => '8 Lab Stations, 40 Lab Stools, Safety Equipment, 50 Lab Goggles',
            'location_details' => 'Ground Floor',
        ]);

        Room::create([
            'name' => 'Bilik Muzik',
            'description' => '10 Musical Instruments, 20 Chairs, 1 Sound System',
            'location_details' => '2nd Floor',
        ]);

        Room::create([
            'name' => 'Pusat Sumber Sekolah',
            'description' => '500 Bookshelves, 50 Reading Tables, 100 Chairs',
            'location_details' => '1st Floor',
        ]);

        Room::create([
            'name' => 'Bilik Moral',
            'description' => '30 Chairs, 5 Whiteboards, 1 Projector',
            'location_details' => 'Ground Floor',
        ]);

        Room::create([
            'name' => 'Bilik Pemulihan',
            'description' => '20 Chairs, 5 Whiteboards, 1 Projector',
            'location_details' => '2nd Floor',
        ]);

        Room::create([
            'name' => 'Bilik Intervensi Kurikulum',
            'description' => '25 Chairs, 5 Whiteboards, 1 Projector',
            'location_details' => '1st Floor',
        ]);
    }
}
