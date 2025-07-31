<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Room;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BookingsExport;
use App\Exports\RoomsExport;
use App\Exports\UsersExport;

class AdminController extends Controller
{

    public function adminLoginForm()
    {
        return view('auth.admin-login'); // Buat view untuk login admin
    }
    
    public function adminLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
    
        // Cek apakah pengguna adalah admin
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password, 'role' => 'admin'])) {
            $request->session()->regenerate();
            return redirect()->route('admin.dashboard');
        }
    
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }
    
    public function index()
    {
        $stats = [
            'total_bookings' => Booking::count(),
            'this_month_bookings' => Booking::whereMonth('created_at', now()->month)->count(),
            'total_users' => User::count(),
            'total_rooms' => Room::count(),
            'pending_bookings' => Booking::where('status', 'pending')->count(),
            'confirmed_bookings' => Booking::where('status', 'confirmed')->count(),
            'cancelled_bookings' => Booking::where('status', 'cancelled')->count(),
            'today_bookings' => Booking::whereDate('booking_date', today())->count(),
        ];
        
        // Chart data untuk dashboard
        $monthlyBookings = Booking::selectRaw('MONTH(booking_date) as month, COUNT(*) as count')
            ->whereYear('booking_date', now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();
        
        $bookingsByStatus = Booking::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get();
        
        // return view('reports.index', compact('stats', 'monthlyBookings', 'bookingsByStatus'));
        return view('admin.dashboard');
    }
    
    public function bookings(Request $request)
    {
        $query = Booking::with(['user', 'room']);
        
        // Filter berdasarkan role user
        $user = auth()->user();
        if (!$user->isAdmin()) {
            if ($user->isManager()) {
                $query->whereIn('room_id', $user->rooms->pluck('id'));
            } else {
                $query->where('user_id', $user->id);
            }
        }
        
        // Filter berdasarkan tanggal
        if ($request->filled('date_from')) {
            $query->where('booking_date', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->where('booking_date', '<=', $request->date_to);
        }
        
        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Filter berdasarkan room
        if ($request->filled('room_id')) {
            $query->where('room_id', $request->room_id);
        }
        
        // Filter berdasarkan user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        
        $bookings = $query->latest()->paginate(20);
        
        // Data untuk filter dropdown
        $rooms = Room::all(['id', 'name']);
        $users = User::all(['id', 'name']);
        
        // Summary statistics
        $summary = [
            'total' => $query->count(),
            'confirmed' => $query->clone()->where('status', 'confirmed')->count(),
            'pending' => $query->clone()->where('status', 'pending')->count(),
            'cancelled' => $query->clone()->where('status', 'cancelled')->count(),
        ];
        
        return view('reports.bookings', compact('bookings', 'rooms', 'users', 'summary'));
    }
    
    public function rooms(Request $request)
    {
        $query = Room::withCount(['bookings' => function($q) use ($request) {
            if ($request->filled('date_from')) {
                $q->where('booking_date', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $q->where('booking_date', '<=', $request->date_to);
            }
            if ($request->filled('status')) {
                $q->where('status', $request->status);
            }
        }]);
        
        // Filter berdasarkan role user
        $user = auth()->user();
        if (!$user->isAdmin()) {
            if ($user->isManager()) {
                $query->whereIn('id', $user->rooms->pluck('id'));
            }
        }
        
        // Filter berdasarkan lokasi
        if ($request->filled('location')) {
            $query->where('location', 'LIKE', '%' . $request->location . '%');
        }
        
        // Filter berdasarkan kapasitas
        if ($request->filled('capacity_min')) {
            $query->where('capacity', '>=', $request->capacity_min);
        }
        
        if ($request->filled('capacity_max')) {
            $query->where('capacity', '<=', $request->capacity_max);
        }
        
        $rooms = $query->get();
        
        // Room usage statistics
        $roomStats = [
            'most_booked' => $rooms->sortByDesc('bookings_count')->first(),
            'least_booked' => $rooms->sortBy('bookings_count')->first(),
            'average_bookings' => $rooms->avg('bookings_count'),
            'total_capacity' => $rooms->sum('capacity'),
        ];
        
        return view('reports.rooms', compact('rooms', 'roomStats'));
    }
    
    public function users(Request $request)
    {
        $query = User::withCount(['bookings' => function($q) use ($request) {
            if ($request->filled('date_from')) {
                $q->where('booking_date', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $q->where('booking_date', '<=', $request->date_to);
            }
            if ($request->filled('status')) {
                $q->where('status', $request->status);
            }
        }]);
        
        // Filter berdasarkan role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }
        
        // Filter berdasarkan department
        if ($request->filled('department')) {
            $query->where('department', $request->department);
        }
        
        $users = $query->orderBy('bookings_count', 'desc')->get();
        
        // User statistics
        $userStats = [
            'total_users' => $users->count(),
            'admins' => $users->where('role', 'admin')->count(),
            'managers' => $users->where('role', 'manager')->count(),
            'regular_users' => $users->where('role', 'user')->count(),
            'most_active' => $users->sortByDesc('bookings_count')->first(),
            'departments' => $users->groupBy('department')->count(),
        ];
        
        return view('reports.users', compact('users', 'userStats'));
    }
    
    public function analytics()
    {
        // Booking trends
        $bookingTrends = Booking::selectRaw('DATE(booking_date) as date, COUNT(*) as count')
            ->whereBetween('booking_date', [now()->subDays(30), now()])
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        // Peak hours analysis
        $peakHours = Booking::selectRaw('HOUR(start_time) as hour, COUNT(*) as count')
            ->whereMonth('booking_date', now()->month)
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();
        
        // Room utilization
        $roomUtilization = Room::withCount(['bookings' => function($query) {
            $query->whereMonth('booking_date', now()->month)
                  ->where('status', 'confirmed');
        }])->get()->map(function($room) {
            $totalHours = 24 * now()->daysInMonth(); // Total available hours in month
            $bookedHours = $room->bookings()->whereMonth('booking_date', now()->month)
                ->where('status', 'confirmed')
                ->get()
                ->sum(function($booking) {
                    return Carbon::parse($booking->end_time)
                        ->diffInHours(Carbon::parse($booking->start_time));
                });
            
            return [
                'room' => $room->name,
                'utilization' => $totalHours > 0 ? ($bookedHours / $totalHours) * 100 : 0,
                'bookings_count' => $room->bookings_count,
            ];
        });
        
        // Booking patterns by day of week
        $bookingsByDayOfWeek = Booking::selectRaw('DAYOFWEEK(booking_date) as day_of_week, COUNT(*) as count')
            ->whereMonth('booking_date', now()->month)
            ->groupBy('day_of_week')
            ->orderBy('day_of_week')
            ->get();
        
        return view('reports.analytics', compact(
            'bookingTrends', 
            'peakHours', 
            'roomUtilization', 
            'bookingsByDayOfWeek'
        ));
    }
    
    public function exportBookings(Request $request)
    {
        $filters = $request->all();
        $filename = 'bookings_' . now()->format('Y-m-d_H-i-s') . '.xlsx';
        
        return Excel::download(new BookingsExport($filters), $filename);
    }
    
    public function exportRooms(Request $request)
    {
        $filters = $request->all();
        $filename = 'rooms_' . now()->format('Y-m-d_H-i-s') . '.xlsx';
        
        return Excel::download(new RoomsExport($filters), $filename);
    }
    
    public function exportUsers(Request $request)
    {
        $filters = $request->all();
        $filename = 'users_' . now()->format('Y-m-d_H-i-s') . '.xlsx';
        
        return Excel::download(new UsersExport($filters), $filename);
    }
    
    public function customReport(Request $request)
    {
        $request->validate([
            'report_type' => 'required|in:bookings,rooms,users,analytics',
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
            'format' => 'required|in:pdf,excel,csv',
        ]);
        
        switch ($request->report_type) {
            case 'bookings':
                return $this->generateBookingReport($request);
            case 'rooms':
                return $this->generateRoomReport($request);
            case 'users':
                return $this->generateUserReport($request);
            case 'analytics':
                return $this->generateAnalyticsReport($request);
        }
    }
    
    private function generateBookingReport($request)
    {
        // Implementation for custom booking report
        // This would generate PDF/Excel based on request parameters
    }
    
    private function generateRoomReport($request)
    {
        // Implementation for custom room report
    }
    
    private function generateUserReport($request)
    {
        // Implementation for custom user report
    }
    
    private function generateAnalyticsReport($request)
    {
        // Implementation for custom analytics report
    }
}