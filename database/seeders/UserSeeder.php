<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin User
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@booking.com',
            'password' => Hash::make('password'),
            'phone' => '081234567890',
            'department' => 'IT',
            'role' => 'admin',
            'employee_id' => 'EMP001',
            'email_verified_at' => now(),
        ]);

        // Manager User
        User::create([
            'name' => 'Manager Fasilitas',
            'email' => 'manager@booking.com',
            'password' => Hash::make('password'),
            'phone' => '081234567891',
            'department' => 'Fasilitas',
            'role' => 'manager',
            'employee_id' => 'EMP002',
            'email_verified_at' => now(),
        ]);

        // Regular Users
        $users = [
            [
                'name' => 'John Doe',
                'email' => 'john@booking.com',
                'department' => 'Marketing',
                'employee_id' => 'EMP003',
                'phone' => '081234567892',
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'jane@booking.com',
                'department' => 'Finance',
                'employee_id' => 'EMP004',
                'phone' => '081234567893',
            ],
            [
                'name' => 'Bob Wilson',
                'email' => 'bob@booking.com',
                'department' => 'HR',
                'employee_id' => 'EMP005',
                'phone' => '081234567894',
            ]
        ];

        foreach ($users as $userData) {
            User::create(array_merge($userData, [
                'password' => Hash::make('password'),
                'role' => 'user',
                'email_verified_at' => now(),
            ]));
        }
    }
}