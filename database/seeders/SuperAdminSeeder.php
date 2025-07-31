<?php

// namespace Database\Seeders;

// use App\Models\User;
// use Illuminate\Database\Seeder;
// use Illuminate\Support\Facades\Hash;

// class AdminUserSeeder extends Seeder
// {
//     public function run()
//     {
//         User::create([
//             'name' => 'Admin',
//             'email' => 'admin@example.com',
//             'password' => Hash::make('password'),
//             'role' => 'admin'
//         ]);
//     }
// }

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@example.com',
            'password' => Hash::make('password'),
            'role' => 'superadmin',
            'email_verified_at' => now()
        ]);
    }
}