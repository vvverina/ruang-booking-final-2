<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Booking;
use App\Models\User;
use App\Models\Room;
use Carbon\Carbon;

class BookingSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::where('role', 'user')->get();
        $rooms = Room::where('status', 'available')->get();

        // Sample bookings untuk hari ini dan besok
        $bookings = [
            [
                'room_code' => 'MR-A001',
                'title' => 'Weekly Team Meeting',
                'description' => 'Meeting rutin tim marketing untuk review progress mingguan.',
                'date' => now()->toDateString(),
                'start_time' => '09:00',
                'end_time' => '10:30',
                'participant_count' => 8,
                'status' => 'confirmed',
                'priority' => 'medium',
            ],
            [
                'room_code' => 'TR-G001',
                'title' => 'Product Training Session',
                'description' => 'Training produk baru untuk tim sales.',
                'date' => now()->toDateString(),
                'start_time' => '14:00',
                'end_time' => '16:00',
                'participant_count' => 15,
                'status' => 'confirmed',
                'priority' => 'high',
            ],
            [
                'room_code' => 'DR-D001',
                'title' => 'Client Consultation',
                'description' => 'Konsultasi dengan klien baru.',
                'date' => now()->addDay()->toDateString(),
                'start_time' => '10:00',
                'end_time' => '11:00',
                'participant_count' => 4,
                'status' => 'pending',
                'priority' => 'high',
            ],
            [
                'room_code' => 'CR-B001',
                'title' => 'Quarterly Review',
                'description' => 'Review kinerja kuartal dengan manajemen.',
                'date' => now()->addDay()->toDateString(),
                'start_time' => '13:00',
                'end_time' => '15:00',
                'participant_count' => 20,
                'status' => 'pending',
                'priority' => 'high',
            ],
            [
                'room_code' => 'MR-A001',
                'title' => 'Project Kickoff',
                'description' => 'Kickoff meeting untuk project baru.',
                'date' => now()->addDays(2)->toDateString(),
                'start_time' => '09:30',
                'end_time' => '11:00',
                'participant_count' => 10,
                'status' => 'confirmed',
                'priority' => 'medium',
            ]
        ];

        foreach ($bookings as $index => $bookingData) {
            $room = Room::where('code', $bookingData['room_code'])->first();
            $user = $users->random();

            if ($room && $user) {
                $booking = Booking::create([
                    'user_id' => $user->id,
                    'room_id' => $room->id,
                    'title' => $bookingData['title'],
                    'description' => $bookingData['description'],
                    'booking_date' => $bookingData['date'],
                    'start_time' => $bookingData['start_time'],
                    'end_time' => $bookingData['end_time'],
                    'participant_count' => $bookingData['participant_count'],
                    'status' => $bookingData['status'],
                    'priority' => $bookingData['priority'],
                    'notes' => 'Sample booking data for testing',
                ]);

                // Jika status confirmed, set confirmed_at dan confirmed_by
                if ($booking->status === 'confirmed') {
                    $manager = User::where('role', 'manager')->first();
                    $booking->update([
                        'confirmed_at' => now(),
                        'confirmed_by' => $manager->id ?? 1,
                    ]);
                }
            }
        }
    }
}