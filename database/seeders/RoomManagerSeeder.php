<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Room;

class RoomManagerSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil user dengan role manager
        $manager = User::where('role', 'manager')->first();
        
        if ($manager) {
            // Assign manager ke beberapa ruangan
            $rooms = Room::whereIn('code', ['CR-B001', 'BR-E001', 'WR-E001'])->get();
            
            foreach ($rooms as $room) {
                $manager->rooms()->attach($room->id);
            }
        }
    }
}