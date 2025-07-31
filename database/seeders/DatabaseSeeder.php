<?php
// namespace Database\Seeders;

// use Illuminate\Database\Seeder;
// use Illuminate\Support\Facades\Hash;
// use App\Models\User;
// use App\Models\Room;

// class DatabaseSeeder extends Seeder {
//     public function run(): void {
//         User::create([
//             'name' => 'Admin',
//             'email' => 'admin@example.com',
//             'password' => Hash::make('password'),
//             'role' => 'admin'
//         ]);
//         \App\Models\User::factory()->create([
//             'name' => 'User Demo',
//             'email' => 'user@example.com',
//             'password' => Hash::make('password'),
//             'role' => 'user'
//         ]);
//         Room::create(['name' => 'Ruang Rapat A', 'description' => 'Lantai 1']);
//         Room::create(['name' => 'Ruang Rapat B', 'description' => 'Lantai 2']);
//     }
// }

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            RoomSeeder::class,
            RoomManagerSeeder::class,
            BookingSeeder::class,
        ]);
    }
}