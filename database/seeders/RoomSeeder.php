<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Room;

class RoomSeeder extends Seeder
{
    public function run(): void
    {
        $rooms = [
            [
                'name' => 'Meeting Room Alpha',
                'code' => 'MR-A001',
                'description' => 'Meeting room dengan fasilitas lengkap untuk presentasi dan diskusi tim.',
                'capacity' => 10,
                'location' => 'Lantai 2, Gedung A',
                'floor' => '2',
                'facilities' => ['TV LED 55"', 'Proyektor', 'AC', 'Whiteboard', 'WiFi', 'Sound System'],
                'price_per_hour' => 50000,
                'status' => 'available',
                'requires_approval' => false,
            ],
            [
                'name' => 'Conference Room Beta',
                'code' => 'CR-B001',
                'description' => 'Ruang konferensi besar untuk meeting dengan banyak peserta.',
                'capacity' => 25,
                'location' => 'Lantai 3, Gedung A',
                'floor' => '3',
                'facilities' => ['TV LED 65"', 'Video Conference', 'AC', 'Microphone', 'WiFi', 'Catering Area'],
                'price_per_hour' => 100000,
                'status' => 'available',
                'requires_approval' => true,
            ],
            [
                'name' => 'Training Room Gamma',
                'code' => 'TR-G001',
                'description' => 'Ruang training dengan meja dan kursi yang dapat diatur ulang.',
                'capacity' => 20,
                'location' => 'Lantai 1, Gedung B',
                'floor' => '1',
                'facilities' => ['Proyektor', 'Screen', 'AC', 'Whiteboard', 'WiFi', 'Flexible Seating'],
                'price_per_hour' => 75000,
                'status' => 'available',
                'requires_approval' => false,
            ],
            [
                'name' => 'Boardroom Executive',
                'code' => 'BR-E001',
                'description' => 'Ruang rapat eksekutif dengan desain mewah dan privasi tinggi.',
                'capacity' => 12,
                'location' => 'Lantai 4, Gedung A',
                'floor' => '4',
                'facilities' => ['Smart TV 75"', 'Video Conference Premium', 'AC', 'Coffee Machine', 'WiFi Premium'],
                'price_per_hour' => 150000,
                'status' => 'available',
                'requires_approval' => true,
            ],
            [
                'name' => 'Discussion Room Delta',
                'code' => 'DR-D001',
                'description' => 'Ruang diskusi kecil untuk meeting tim atau brainstorming.',
                'capacity' => 6,
                'location' => 'Lantai 2, Gedung B',
                'floor' => '2',
                'facilities' => ['TV 43"', 'AC', 'Whiteboard', 'WiFi'],
                'price_per_hour' => 30000,
                'status' => 'available',
                'requires_approval' => false,
            ],
            [
                'name' => 'Workshop Room Epsilon',
                'code' => 'WR-E001',
                'description' => 'Ruang workshop dengan space terbuka untuk kegiatan praktis.',
                'capacity' => 30,
                'location' => 'Lantai 1, Gedung C',
                'floor' => '1',
                'facilities' => ['Proyektor', 'Sound System', 'AC', 'Storage Area', 'WiFi'],
                'price_per_hour' => 80000,
                'status' => 'maintenance',
                'requires_approval' => false,
            ]
        ];

        foreach ($rooms as $room) {
            Room::create($room);
        }
    }
}