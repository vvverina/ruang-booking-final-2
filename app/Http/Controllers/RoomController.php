<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Booking;
use Illuminate\Http\Request;
use Carbon\Carbon;

class RoomController extends Controller
{
    public function index(Request $request)
    {
        $query = Room::query();
        
        // Filter berdasarkan parameter
        if ($request->filled('capacity')) {
            $query->where('capacity', '>=', $request->capacity);
        }
        
        if ($request->filled('location')) {
            $query->where('location', 'LIKE', '%' . $request->location . '%');
        }
        
        if ($request->filled('facilities')) {
            $facilities = $request->facilities;
            $query->where(function($q) use ($facilities) {
                foreach ($facilities as $facility) {
                    $q->whereJsonContains('facilities', $facility);
                }
            });
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            $query->where('status', 'available');
        }
        
        $rooms = $query->paginate(12);
        
        // Data untuk filter
        $locations = Room::distinct()->pluck('location');
        $allFacilities = Room::whereNotNull('facilities')
            ->get()
            ->pluck('facilities')
            ->flatten()
            ->unique()
            ->values();
        
        return view('rooms.index', compact('rooms', 'locations', 'allFacilities'));
    }
    
    public function show(Room $room)
    {
        // Booking hari ini
        $todayBookings = $room->bookings()
            ->where('booking_date', today())
            ->where('status', '!=', 'cancelled')
            ->orderBy('start_time')
            ->get();
        
        // Booking minggu ini
        $weekBookings = $room->bookings()
            ->whereBetween('booking_date', [now()->startOfWeek(), now()->endOfWeek()])
            ->where('status', '!=', 'cancelled')
            ->with('user')
            ->orderBy('booking_date')
            ->orderBy('start_time')
            ->get();
        
        return view('rooms.show', compact('room', 'todayBookings', 'weekBookings'));
    }
    
    public function calendar(Room $room)
    {
        return view('rooms.calendar', compact('room'));
    }
    
    public function searchAvailable(Request $request)
    {
        $date = $request->get('date', today()->toDateString());
        $startTime = $request->get('start_time');
        $endTime = $request->get('end_time');
        $capacity = $request->get('capacity', 1);
        
        $availableRooms = Room::available()
            ->where('capacity', '>=', $capacity)
            ->get()
            ->filter(function($room) use ($date, $startTime, $endTime) {
                return $room->isAvailableAt($date, $startTime, $endTime);
            });
        
        return response()->json($availableRooms);
    }
    
    // API Methods
    public function apiIndex()
    {
        return Room::available()->get();
    }
    
    public function apiShow(Room $room)
    {
        return $room->load(['bookings' => function($query) {
            $query->where('booking_date', '>=', today())
                  ->where('status', '!=', 'cancelled');
        }]);
    }
    
    public function apiAvailability(Room $room, $date)
    {
        $bookings = $room->bookings()
            ->where('booking_date', $date)
            ->where('status', '!=', 'cancelled')
            ->select('start_time', 'end_time', 'title')
            ->get();
        
        return response()->json($bookings);
    }
    
    public function apiSearch(Request $request)
    {
        $query = Room::query();
        
        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('code', 'LIKE', "%{$search}%")
                  ->orWhere('location', 'LIKE', "%{$search}%");
            });
        }
        
        return $query->available()->limit(10)->get();
    }
    
    public function currentBooking(Room $room)
    {
        $now = now();
        $currentBooking = $room->bookings()
            ->where('booking_date', $now->toDateString())
            ->where('start_time', '<=', $now->format('H:i:s'))
            ->where('end_time', '>=', $now->format('H:i:s'))
            ->where('status', 'confirmed')
            ->with('user')
            ->first();
        
        return response()->json($currentBooking);
    }
    
    public function todaySchedule(Room $room)
    {
        $schedule = $room->bookings()
            ->where('booking_date', today())
            ->where('status', '!=', 'cancelled')
            ->orderBy('start_time')
            ->get(['title', 'start_time', 'end_time', 'status']);
        
        return response()->json($schedule);
    }
    
    public function displayRooms()
    {
        $rooms = Room::available()
            ->with(['bookings' => function($query) {
                $query->where('booking_date', today())
                      ->where('status', '!=', 'cancelled')
                      ->orderBy('start_time');
            }])
            ->get();
        
        return response()->json($rooms);
    }
}