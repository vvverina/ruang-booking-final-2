<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Room;
use App\Models\BookingHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Carbon\Carbon;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Booking::with(['room', 'user']);
        
        // Filter berdasarkan role
        if (!$user->isAdmin()) {
            if ($user->isManager()) {
                $query->whereIn('room_id', $user->rooms->pluck('id'));
            } else {
                $query->where('user_id', $user->id);
            }
        }
        
        // Filter berdasarkan parameter
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
        
        $bookings = $query->latest()->paginate(15);
        $rooms = Room::available()->get(['id', 'name']);
        
        return view('bookings.index', compact('bookings', 'rooms'));
    }
    
    public function create(Request $request)
    {
        $rooms = Room::available()->get();
        $selectedRoom = null;
        
        if ($request->filled('room_id')) {
            $selectedRoom = Room::find($request->room_id);
        }
        
        return view('bookings.create', compact('rooms', 'selectedRoom'));
    }
    
    public function store(Request $request)
{
    // Validasi
    $request->validate([
        'room_id' => 'required|exists:rooms,id',
        'booking_date' => 'required|date',
    ]);

    // Simpan booking
    Booking::create([
        'user_id' => auth()->id(),
        'room_id' => $request->room_id,
        'booking_date' => $request->booking_date,
        'status' => 'pending', // atau sesuai logic kamu
    ]);

    return redirect()->route('my-bookings.index')->with('success', 'Booking berhasil dikirim.');
}

    
    public function show(Booking $booking)
    {
        Gate::authorize('view', $booking);
        
        $booking->load(['room', 'user', 'confirmedBy', 'histories.user']);
        
        return view('bookings.show', compact('booking'));
    }
    
    public function edit(Booking $booking)
    {
        Gate::authorize('update', $booking);
        
        if (!$booking->canBeCancelled()) {
            return redirect()->route('bookings.show', $booking)
                ->with('error', 'Booking tidak dapat diubah.');
        }
        
        $rooms = Room::available()->get();
        
        return view('bookings.edit', compact('booking', 'rooms'));
    }
    
    public function update(Request $request, Booking $booking)
    {
        Gate::authorize('update', $booking);
        
        if (!$booking->canBeCancelled()) {
            return redirect()->route('bookings.show', $booking)
                ->with('error', 'Booking tidak dapat diubah.');
        }
        
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'booking_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'participant_count' => 'required|integer|min:1',
            'notes' => 'nullable|string',
        ]);
        
        $oldValues = $booking->toArray();
        
        $booking->update($request->only([
            'title', 'description', 'booking_date', 
            'start_time', 'end_time', 'participant_count', 'notes'
        ]));
        
        // Log history
        BookingHistory::create([
            'booking_id' => $booking->id,
            'user_id' => auth()->id(),
            'action' => 'updated',
            'description' => 'Booking diperbarui',
            'old_values' => $oldValues,
            'new_values' => $booking->fresh()->toArray(),
        ]);
        
        return redirect()->route('bookings.show', $booking)
            ->with('success', 'Booking berhasil diperbarui!');
    }
    
    public function cancel(Request $request, Booking $booking)
    {
        Gate::authorize('cancel', $booking);
        
        if (!$booking->canBeCancelled()) {
            return back()->with('error', 'Booking tidak dapat dibatalkan.');
        }
        
        $booking->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancellation_reason' => $request->cancellation_reason,
        ]);
        
        // Log history
        BookingHistory::create([
            'booking_id' => $booking->id,
            'user_id' => auth()->id(),
            'action' => 'cancelled',
            'description' => 'Booking dibatalkan: ' . $request->cancellation_reason,
        ]);
        
        return redirect()->route('bookings.index')
            ->with('success', 'Booking berhasil dibatalkan.');
    }
    
    public function confirm(Booking $booking)
    {
        Gate::authorize('confirm', $booking);
        
        $booking->update([
            'status' => 'confirmed',
            'confirmed_at' => now(),
            'confirmed_by' => auth()->id(),
        ]);
        
        // Log history
        BookingHistory::create([
            'booking_id' => $booking->id,
            'user_id' => auth()->id(),
            'action' => 'confirmed',
            'description' => 'Booking dikonfirmasi',
        ]);
        
        return back()->with('success', 'Booking berhasil dikonfirmasi.');
    }
    
    public function reject(Request $request, Booking $booking)
    {
        Gate::authorize('reject', $booking);
        
        $booking->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancellation_reason' => $request->rejection_reason ?? 'Ditolak oleh manager',
        ]);
        
        // Log history
        BookingHistory::create([
            'booking_id' => $booking->id,
            'user_id' => auth()->id(),
            'action' => 'rejected',
            'description' => 'Booking ditolak: ' . ($request->rejection_reason ?? 'Tidak ada alasan'),
        ]);
        
        return back()->with('success', 'Booking berhasil ditolak.');
    }
    
    public function myBookings()
    {
        $bookings = auth()->user()->bookings()
            ->with('room')
            ->latest()
            ->paginate(15);
        
        return view('bookings.my-bookings', compact('bookings'));
    }
    
    public function myBookingHistory()
    {
        $bookings = auth()->user()->bookings()
            ->with(['room', 'histories.user'])
            ->latest()
            ->paginate(15);
        
        return view('bookings.history', compact('bookings'));
    }
    
    public function calendarEvents(Request $request)
    {
        $user = auth()->user();
        $query = Booking::with(['room', 'user']);
        
        // Filter berdasarkan role
        if (!$user->isAdmin()) {
            if ($user->isManager()) {
                $query->whereIn('room_id', $user->rooms->pluck('id'));
            } else {
                $query->where('user_id', $user->id);
            }
        }
        
        if ($request->filled('start') && $request->filled('end')) {
            $query->whereBetween('booking_date', [
                Carbon::parse($request->start)->startOfDay(),
                Carbon::parse($request->end)->endOfDay()
            ]);
        }
        
        $bookings = $query->where('status', '!=', 'cancelled')->get();
        
        $events = $bookings->map(function($booking) {
            return [
                'id' => $booking->id,
                'title' => $booking->title . ' (' . $booking->room->name . ')',
                'start' => $booking->booking_date . 'T' . $booking->start_time,
                'end' => $booking->booking_date . 'T' . $booking->end_time,
                'backgroundColor' => $this->getStatusColor($booking->status),
                'borderColor' => $this->getStatusColor($booking->status),
                'url' => route('bookings.show', $booking),
            ];
        });
        
        return response()->json($events);
    }
    
    public function roomAvailability(Room $room, Request $request)
    {
        $date = $request->get('date', today()->toDateString());
        
        $bookings = $room->bookings()
            ->where('booking_date', $date)
            ->where('status', '!=', 'cancelled')
            ->orderBy('start_time')
            ->get(['start_time', 'end_time', 'title', 'status']);
        
        return response()->json($bookings);
    }
    
    private function getStatusColor($status)
    {
        return match($status) {
            'pending' => '#ffc107',
            'confirmed' => '#28a745',
            'cancelled' => '#dc3545',
            'completed' => '#6c757d',
            default => '#007bff'
        };
    }
    
    // API Methods
    public function apiIndex()
    {
        $user = auth()->user();
        $query = Booking::with(['room', 'user']);
        
        if (!$user->isAdmin()) {
            $query->where('user_id', $user->id);
        }
        
        return $query->latest()->get();
    }
    
    public function apiStore(Request $request)
    {
        // Same validation and logic as store method
        return $this->store($request);
    }
    
    public function apiShow(Booking $booking)
    {
        Gate::authorize('view', $booking);
        return $booking->load(['room', 'user', 'confirmedBy']);
    }
    
    public function apiUpdate(Request $request, Booking $booking)
    {
        return $this->update($request, $booking);
    }
    
    public function apiDestroy(Booking $booking)
    {
        Gate::authorize('delete', $booking);
        
        if (!$booking->canBeCancelled()) {
            return response()->json(['error' => 'Booking cannot be cancelled'], 400);
        }
        
        $booking->update(['status' => 'cancelled', 'cancelled_at' => now()]);
        
        return response()->json(['message' => 'Booking cancelled successfully']);
    }
}