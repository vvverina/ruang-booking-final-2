<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Room;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Statistics berdasarkan role
        if ($user->isAdmin()) {
            $stats = [
                'total_rooms' => Room::count(),
                'total_users' => User::count(),
                'total_bookings' => Booking::count(),
                'pending_bookings' => Booking::where('status', 'pending')->count(),
                'today_bookings' => Booking::whereDate('booking_date', today())->count(),
                'this_month_bookings' => Booking::whereMonth('created_at', now()->month)->count(),
            ];
            
            $recentBookings = Booking::with(['user', 'room'])
                ->latest()
                ->take(5)
                ->get();
                
            $roomUsage = Room::withCount(['bookings' => function($query) {
                $query->whereMonth('booking_date', now()->month);
            }])->orderBy('bookings_count', 'desc')->take(5)->get();
            
        } elseif ($user->isManager()) {
            $managedRooms = $user->rooms->pluck('id');
            
            $stats = [
                'managed_rooms' => $user->rooms->count(),
                'pending_approvals' => Booking::whereIn('room_id', $managedRooms)
                    ->where('status', 'pending')
                    ->count(),
                'today_bookings' => Booking::whereIn('room_id', $managedRooms)
                    ->whereDate('booking_date', today())
                    ->count(),
                'this_week_bookings' => Booking::whereIn('room_id', $managedRooms)
                    ->whereBetween('booking_date', [now()->startOfWeek(), now()->endOfWeek()])
                    ->count(),
            ];
            
            $recentBookings = Booking::with(['user', 'room'])
                ->whereIn('room_id', $managedRooms)
                ->latest()
                ->take(5)
                ->get();
                
            $roomUsage = Room::whereIn('id', $managedRooms)
                ->withCount(['bookings' => function($query) {
                    $query->whereMonth('booking_date', now()->month);
                }])
                ->orderBy('bookings_count', 'desc')
                ->get();
                
        } else {
            $stats = [
                'my_bookings' => $user->bookings()->count(),
                'upcoming_bookings' => $user->bookings()
                    ->where('booking_date', '>=', today())
                    ->where('status', '!=', 'cancelled')
                    ->count(),
                'pending_bookings' => $user->bookings()
                    ->where('status', 'pending')
                    ->count(),
                'this_month_bookings' => $user->bookings()
                    ->whereMonth('booking_date', now()->month)
                    ->count(),
            ];
            
            $recentBookings = $user->bookings()
                ->with('room')
                ->latest()
                ->take(5)
                ->get();
                
            $roomUsage = null;
        }
        
        // Upcoming events untuk semua user
        $upcomingEvents = Booking::with(['room', 'user'])
            ->where('booking_date', '>=', today())
            ->where('booking_date', '<=', today()->addDays(7))
            ->where('status', 'confirmed')
            ->when(!$user->isAdmin(), function($query) use ($user) {
                if ($user->isManager()) {
                    return $query->whereIn('room_id', $user->rooms->pluck('id'));
                } else {
                    return $query->where('user_id', $user->id);
                }
            })
            ->orderBy('booking_date')
            ->orderBy('start_time')
            ->take(10)
            ->get();
        
        return view('dashboard', compact('stats', 'recentBookings', 'upcomingEvents', 'roomUsage'));
    }
}