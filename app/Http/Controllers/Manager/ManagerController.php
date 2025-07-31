<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Room;
use App\Models\User;
use App\Models\BookingHistory;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }
    
    public function dashboard()
    {
        $stats = [
            'total_users' => User::count(),
            'total_rooms' => Room::count(),
            'total_bookings' => Booking::count(),
            'pending_bookings' => Booking::where('status', 'pending')->count(),
            'confirmed_bookings' => Booking::where('status', 'confirmed')->count(),
            'cancelled_bookings' => Booking::where('status', 'cancelled')->count(),
            'today_bookings' => Booking::whereDate('booking_date', today())->count(),
            'this_month_bookings' => Booking::whereMonth('booking_date', now()->month)->count(),
            'active_users' => User::whereHas('bookings', function($query) {
                $query->whereMonth('booking_date', now()->month);
            })->count(),
            'revenue_this_month' => Booking::whereMonth('booking_date', now()->month)
                ->where('status', 'confirmed')
                ->with('room')
                ->get()
                ->sum(function($booking) {
                    $hours = Carbon::parse($booking->end_time)
                        ->diffInHours(Carbon::parse($booking->start_time));
                    return $hours * $booking->room->price_per_hour;
                }),
        ];
        
        // Chart data untuk booking per bulan (12 bulan terakhir)
        $monthlyBookings = collect();
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $count = Booking::whereYear('booking_date', $date->year)
                ->whereMonth('booking_date', $date->month)
                ->count();
            
            $monthlyBookings->push([
                'month' => $date->format('M Y'),
                'count' => $count
            ]);
        }
        
        // Top 5 ruangan paling sering dibook
        $topRooms = Room::withCount(['bookings' => function($query) {
            $query->whereMonth('booking_date', now()->month);
        }])->orderBy('bookings_count', 'desc')->take(5)->get();
        
        // Recent activities
        $recentActivities = BookingHistory::with(['booking.room', 'user'])
            ->latest()
            ->take(10)
            ->get();
        
        // System health indicators
        $systemHealth = [
            'database_size' => $this->getDatabaseSize(),
            'storage_used' => $this->getStorageUsed(),
            'cache_status' => $this->getCacheStatus(),
            'queue_jobs' => $this->getQueueJobsCount(),
        ];
        
        return view('admin.dashboard', compact(
            'stats', 
            'monthlyBookings', 
            'topRooms', 
            'recentActivities',
            'systemHealth'
        ));
    }
    
    // Room Management
    public function rooms(Request $request)
    {
        $query = Room::withCount('bookings');
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('code', 'LIKE', "%{$search}%")
                  ->orWhere('location', 'LIKE', "%{$search}%");
            });
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('location')) {
            $query->where('location', 'LIKE', '%' . $request->location . '%');
        }
        
        $rooms = $query->latest()->paginate(15);
        
        return view('admin.rooms.index', compact('rooms'));
    }
    
    public function createRoom()
    {
        $locations = Room::distinct()->pluck('location')->filter();
        $facilities = $this->getAvailableFacilities();
        
        return view('admin.rooms.create', compact('locations', 'facilities'));
    }
    
    public function storeRoom(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:rooms',
            'description' => 'nullable|string',
            'capacity' => 'required|integer|min:1',
            'location' => 'required|string|max:255',
            'floor' => 'nullable|string|max:10',
            'facilities' => 'nullable|array',
            'price_per_hour' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'requires_approval' => 'boolean',
            'booking_rules' => 'nullable|string',
            'operating_hours_start' => 'nullable|date_format:H:i',
            'operating_hours_end' => 'nullable|date_format:H:i',
        ]);
        
        $data = $request->except('image');
        $data['requires_approval'] = $request->has('requires_approval');
        
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('rooms', 'public');
        }
        
        Room::create($data);
        
        Log::info('Room created by admin', [
            'admin_id' => auth()->id(),
            'room_name' => $request->name,
            'room_code' => $request->code
        ]);
        
        return redirect()->route('admin.rooms.index')
            ->with('success', 'Ruangan berhasil dibuat!');
    }
    
    public function showRoom(Room $room)
    {
        $room->load(['managers', 'bookings' => function($query) {
            $query->latest()->take(10);
        }]);
        
        $roomStats = [
            'total_bookings' => $room->bookings()->count(),
            'this_month_bookings' => $room->bookings()
                ->whereMonth('booking_date', now()->month)->count(),
            'pending_bookings' => $room->bookings()
                ->where('status', 'pending')->count(),
            'utilization_rate' => $this->calculateRoomUtilization($room),
            'revenue_this_month' => $this->calculateRoomRevenue($room),
        ];
        
        return view('admin.rooms.show', compact('room', 'roomStats'));
    }
    
    public function editRoom(Room $room)
    {
        $locations = Room::distinct()->pluck('location')->filter();
        $facilities = $this->getAvailableFacilities();
        
        return view('admin.rooms.edit', compact('room', 'locations', 'facilities'));
    }
    
    public function updateRoom(Request $request, Room $room)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:rooms,code,' . $room->id,
            'description' => 'nullable|string',
            'capacity' => 'required|integer|min:1',
            'location' => 'required|string|max:255',
            'floor' => 'nullable|string|max:10',
            'facilities' => 'nullable|array',
            'price_per_hour' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'requires_approval' => 'boolean',
            'booking_rules' => 'nullable|string',
            'operating_hours_start' => 'nullable|date_format:H:i',
            'operating_hours_end' => 'nullable|date_format:H:i',
        ]);
        
        $data = $request->except('image');
        $data['requires_approval'] = $request->has('requires_approval');
        
        if ($request->hasFile('image')) {
            // Delete old image
            if ($room->image) {
                Storage::disk('public')->delete($room->image);
            }
            $data['image'] = $request->file('image')->store('rooms', 'public');
        }
        
        $room->update($data);
        
        Log::info('Room updated by admin', [
            'admin_id' => auth()->id(),
            'room_id' => $room->id,
            'room_name' => $request->name
        ]);
        
        return redirect()->route('admin.rooms.index')
            ->with('success', 'Ruangan berhasil diperbarui!');
    }
    
    public function destroyRoom(Room $room)
    {
        if ($room->bookings()->whereIn('status', ['pending', 'confirmed'])->exists()) {
            return back()->with('error', 'Tidak dapat menghapus ruangan yang memiliki booking aktif.');
        }
        
        if ($room->image) {
            Storage::disk('public')->delete($room->image);
        }
        
        $roomName = $room->name;
        $room->delete();
        
        Log::info('Room deleted by admin', [
            'admin_id' => auth()->id(),
            'room_name' => $roomName
        ]);
        
        return redirect()->route('admin.rooms.index')
            ->with('success', 'Ruangan berhasil dihapus!');
    }
    
    public function toggleRoomStatus(Room $room)
    {
        $newStatus = $room->status === 'available' ? 'unavailable' : 'available';
        $room->update(['status' => $newStatus]);
        
        Log::info('Room status changed by admin', [
            'admin_id' => auth()->id(),
            'room_id' => $room->id,
            'old_status' => $room->status,
            'new_status' => $newStatus
        ]);
        
        return back()->with('success', 'Status ruangan berhasil diubah!');
    }
    
    // Room Manager Assignment
    public function roomManagers(Room $room)
    {
        $room->load('managers');
        $availableManagers = User::where('role', 'manager')
            ->whereNotIn('id', $room->managers->pluck('id'))
            ->get();
        
        return view('admin.rooms.managers', compact('room', 'availableManagers'));
    }
    
    public function assignManager(Request $request, Room $room)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);
        
        $user = User::findOrFail($request->user_id);
        
        if ($user->role !== 'manager') {
            return back()->with('error', 'User harus memiliki role manager.');
        }
        
        if ($room->managers()->where('user_id', $user->id)->exists()) {
            return back()->with('error', 'Manager sudah ditugaskan ke ruangan ini.');
        }
        
        $room->managers()->attach($user->id, ['assigned_at' => now()]);
        
        Log::info('Manager assigned to room', [
            'admin_id' => auth()->id(),
            'manager_id' => $user->id,
            'room_id' => $room->id
        ]);
        
        return back()->with('success', 'Manager berhasil ditambahkan!');
    }
    
    public function removeManager(Room $room, User $user)
    {
        $room->managers()->detach($user->id);
        
        Log::info('Manager removed from room', [
            'admin_id' => auth()->id(),
            'manager_id' => $user->id,
            'room_id' => $room->id
        ]);
        
        return back()->with('success', 'Manager berhasil dihapus!');
    }
    
    // Booking Management
    public function bookings(Request $request)
    {
        $query = Booking::with(['user', 'room', 'confirmedBy']);
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('date_from')) {
            $query->where('booking_date', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->where('booking_date', '<=', $request->date_to);
        }
        
        if ($request->filled('room_id')) {
            $query->where('room_id', $request->room_id);
        }
        
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        
        $bookings = $query->latest()->paginate(20);
        $rooms = Room::all(['id', 'name']);
        $users = User::all(['id', 'name']);
        
        return view('admin.bookings.index', compact('bookings', 'rooms', 'users'));
    }
    
    public function showBooking(Booking $booking)
    {
        $booking->load(['user', 'room', 'confirmedBy', 'histories.user']);
        
        return view('admin.bookings.show', compact('booking'));
    }
    
    public function forceConfirm(Booking $booking)
    {
        if ($booking->status === 'confirmed') {
            return back()->with('error', 'Booking sudah dikonfirmasi.');
        }
        
        $booking->update([
            'status' => 'confirmed',
            'confirmed_at' => now(),
            'confirmed_by' => auth()->id(),
        ]);
        
        BookingHistory::create([
            'booking_id' => $booking->id,
            'user_id' => auth()->id(),
            'action' => 'force_confirmed',
            'description' => 'Booking dikonfirmasi paksa oleh admin',
        ]);
        
        Log::info('Booking force confirmed by admin', [
            'admin_id' => auth()->id(),
            'booking_id' => $booking->id
        ]);
        
        return back()->with('success', 'Booking berhasil dikonfirmasi!');
    }
    
    public function forceCancel(Request $request, Booking $booking)
    {
        if ($booking->status === 'cancelled') {
            return back()->with('error', 'Booking sudah dibatalkan.');
        }
        
        $booking->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancellation_reason' => $request->reason ?? 'Dibatalkan paksa oleh admin',
        ]);
        
        BookingHistory::create([
            'booking_id' => $booking->id,
            'user_id' => auth()->id(),
            'action' => 'force_cancelled',
            'description' => 'Booking dibatalkan paksa oleh admin: ' . ($request->reason ?? 'Tidak ada alasan'),
        ]);
        
        Log::info('Booking force cancelled by admin', [
            'admin_id' => auth()->id(),
            'booking_id' => $booking->id,
            'reason' => $request->reason ?? 'No reason provided'
        ]);
        
        return back()->with('success', 'Booking berhasil dibatalkan!');
    }
    
    // User Management
    public function users(Request $request)
    {
        $query = User::withCount('bookings');
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('employee_id', 'LIKE', "%{$search}%");
            });
        }
        
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }
        
        if ($request->filled('department')) {
            $query->where('department', $request->department);
        }
        
        $users = $query->latest()->paginate(15);
        $departments = User::distinct()->whereNotNull('department')->pluck('department');
        
        return view('admin.users.index', compact('users', 'departments'));
    }
    
    // System Settings
    public function settings()
    {
        $settings = SystemSetting::pluck('value', 'key');
        
        return view('admin.settings.index', compact('settings'));
    }
    
    public function updateSettings(Request $request)
    {
        $request->validate([
            'site_name' => 'required|string|max:255',
            'site_description' => 'nullable|string',
            'booking_advance_days' => 'required|integer|min:1|max:365',
            'booking_cancel_hours' => 'required|integer|min:1|max:168',
            'auto_confirm_bookings' => 'boolean',
            'email_notifications' => 'boolean',
            'maintenance_mode' => 'boolean',
        ]);
        
        foreach ($request->except('_token') as $key => $value) {
            SystemSetting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }
        
        Log::info('System settings updated by admin', [
            'admin_id' => auth()->id(),
            'settings' => $request->except('_token')
        ]);
        
        return back()->with('success', 'Settings berhasil diperbarui!');
    }
    
    public function backup()
    {
        try {
            // Run database backup
            Artisan::call('backup:run');
            
            Log::info('Database backup created by admin', [
                'admin_id' => auth()->id()
            ]);
            
            return back()->with('success', 'Backup berhasil dibuat!');
        } catch (\Exception $e) {
            Log::error('Database backup failed', [
                'admin_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);
            
            return back()->with('error', 'Backup gagal dibuat: ' . $e->getMessage());
        }
    }
    
    public function logs()
    {
        $logPath = storage_path('logs/laravel.log');
        $logs = [];
        
        if (file_exists($logPath)) {
            $logContent = file_get_contents($logPath);
            $logLines = array_reverse(explode("\n", $logContent));
            
            // Parse only recent logs (last 100 lines)
            $logLines = array_slice($logLines, 0, 100);
            
            foreach ($logLines as $line) {
                if (!empty(trim($line))) {
                    $logs[] = $this->parseLogLine($line);
                }
            }
        }
        
        return view('admin.logs.index', compact('logs'));
    }
    
    private function parseLogLine($line)
    {
        // Basic log parsing - you might want to use a more sophisticated parser
        preg_match('/\[(.*?)\] (\w+)\.(\w+): (.*)/', $line, $matches);
        
        if (count($matches) >= 5) {
            return [
                'timestamp' => $matches[1],
                'level' => $matches[2],
                'channel' => $matches[3],
                'message' => $matches[4],
                'raw' => $line
            ];
        }
        
        return [
            'timestamp' => 'N/A',
            'level' => 'INFO',
            'channel' => 'system',
            'message' => $line,
            'raw' => $line
        ];
    }
    
    public function clearLogs()
    {
        $logPath = storage_path('logs/laravel.log');
        
        if (file_exists($logPath)) {
            file_put_contents($logPath, '');
            
            Log::info('Logs cleared by admin', [
                'admin_id' => auth()->id()
            ]);
            
            return back()->with('success', 'Logs berhasil dibersihkan!');
        }
        
        return back()->with('error', 'File log tidak ditemukan.');
    }
    
    // Analytics
    public function analytics()
    {
        $bookingsByMonth = Booking::selectRaw('MONTH(booking_date) as month, YEAR(booking_date) as year, COUNT(*) as count')
            ->whereYear('booking_date', '>=', now()->subYear()->year)
            ->groupBy('month', 'year')
            ->orderBy('year')
            ->orderBy('month')
            ->get();
        
        $bookingsByStatus = Booking::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get();
        
        $roomUsage = Room::withCount(['bookings' => function($query) {
            $query->whereMonth('booking_date', now()->month);
        }])->orderBy('bookings_count', 'desc')->get();
        
        $userActivity = User::withCount(['bookings' => function($query) {
            $query->whereMonth('booking_date', now()->month);
        }])->orderBy('bookings_count', 'desc')->take(10)->get();
        
        // Peak hours analysis
        $peakHours = Booking::selectRaw('HOUR(start_time) as hour, COUNT(*) as count')
            ->whereMonth('booking_date', now()->month)
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();
        
        // Revenue analysis
        $monthlyRevenue = Booking::with('room')
            ->where('status', 'confirmed')
            ->whereYear('booking_date', now()->year)
            ->get()
            ->groupBy(function($booking) {
                return $booking->booking_date->format('Y-m');
            })
            ->map(function($bookings) {
                return $bookings->sum(function($booking) {
                    $hours = Carbon::parse($booking->end_time)
                        ->diffInHours(Carbon::parse($booking->start_time));
                    return $hours * $booking->room->price_per_hour;
                });
            });
        
        return view('admin.analytics.index', compact(
            'bookingsByMonth', 
            'bookingsByStatus', 
            'roomUsage', 
            'userActivity',
            'peakHours',
            'monthlyRevenue'
        ));
    }
    
    // API Methods for Charts
    public function bookingsChart()
    {
        $data = Booking::selectRaw('DATE(booking_date) as date, COUNT(*) as count')
            ->whereBetween('booking_date', [now()->subDays(30), now()])
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        return response()->json($data);
    }
    
    public function roomsUsage()
    {
        $data = Room::withCount(['bookings' => function($query) {
            $query->whereMonth('booking_date', now()->month);
        }])->get(['name', 'bookings_count']);
        
        return response()->json($data);
    }
    
    public function usersActivity()
    {
        $data = User::withCount(['bookings' => function($query) {
            $query->whereMonth('booking_date', now()->month);
        }])->orderBy('bookings_count', 'desc')->take(10)->get(['name', 'bookings_count']);
        
        return response()->json($data);
    }
    
    public function revenueChart()
    {
        $data = collect();
        
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $revenue = Booking::with('room')
                ->where('status', 'confirmed')
                ->whereYear('booking_date', $date->year)
                ->whereMonth('booking_date', $date->month)
                ->get()
                ->sum(function($booking) {
                    $hours = Carbon::parse($booking->end_time)
                        ->diffInHours(Carbon::parse($booking->start_time));
                    return $hours * $booking->room->price_per_hour;
                });
            
            $data->push([
                'month' => $date->format('M Y'),
                'revenue' => $revenue
            ]);
        }
        
        return response()->json($data);
    }
    
    // Helper Methods
    private function getAvailableFacilities()
    {
        return [
            'Projector', 'WiFi', 'Air Conditioning', 'Whiteboard', 
            'Sound System', 'Microphone', 'Video Conference', 
            'Flip Chart', 'Markers', 'Extension Cable'
        ];
    }
    
    private function calculateRoomUtilization(Room $room)
    {
        $totalHours = 24 * now()->daysInMonth();
        $bookedHours = $room->bookings()
            ->whereMonth('booking_date', now()->month)
            ->where('status', 'confirmed')
            ->get()
            ->sum(function($booking) {
                return Carbon::parse($booking->end_time)
                    ->diffInHours(Carbon::parse($booking->start_time));
            });
        
        return $totalHours > 0 ? ($bookedHours / $totalHours) * 100 : 0;
    }
    
    private function calculateRoomRevenue(Room $room)
    {
        return $room->bookings()
            ->whereMonth('booking_date', now()->month)
            ->where('status', 'confirmed')
            ->get()
            ->sum(function($booking) {
                $hours = Carbon::parse($booking->end_time)
                    ->diffInHours(Carbon::parse($booking->start_time));
                return $hours * $room->price_per_hour;
            });
    }
    
    private function getDatabaseSize()
    {
        try {
            $size = DB::select("SELECT 
                ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS 'size_mb' 
                FROM information_schema.tables 
                WHERE table_schema = DATABASE()")[0]->size_mb ?? 0;
            
            return $size . ' MB';
        } catch (\Exception $e) {
            return 'N/A';
        }
    }
    
    private function getStorageUsed()
    {
        try {
            $bytes = disk_total_space(storage_path()) - disk_free_space(storage_path());
            $mb = round($bytes / 1024 / 1024, 2);
            return $mb . ' MB';
        } catch (\Exception $e) {
            return 'N/A';
        }
    }
    
    private function getCacheStatus()
    {
        try {
            return cache()->has('admin_cache_test') ? 'Active' : 'Inactive';
        } catch (\Exception $e) {
            return 'Error';
        }
    }
    
    private function getQueueJobsCount()
    {
        try {
            return DB::table('jobs')->count();
        } catch (\Exception $e) {
            return 0;
        }
    }
}