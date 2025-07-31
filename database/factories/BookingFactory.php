<?php
namespace Database\Factories;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Booking;
use App\Models\User;
use App\Models\Room;

class BookingFactory extends Factory {
    protected $model = Booking::class;
    public function definition(): array {
        return [
            'user_id' => User::factory(),
            'room_id' => Room::factory(),
            'booking_date' => now()->addDays(rand(1, 10))->toDateString(),
            'status' => 'pending'
        ];
    }
}
