<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Room;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $stats = [
            'total_bookings' => Booking::count(),
            'pending_bookings' => Booking::where('status', 'pending')->count(),
            'confirmed_bookings' => Booking::where('status', 'confirmed')->count(),
            'cancelled_bookings' => Booking::where('status', 'cancelled')->count(),
            'today_bookings' => Booking::whereDate('created_at', $today)->count(),
            'total_rooms' => Room::count(),
            'total_users' => User::count(),
        ];

        $recentBookings = Booking::with(['room', 'user'])
        ->orderBy('created_at', 'desc')
        ->limit(5)
        ->get();

    return view('admin.dashboard', compact('stats', 'recentBookings'));
    }
}
