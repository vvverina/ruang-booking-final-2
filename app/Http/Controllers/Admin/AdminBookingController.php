<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Room;
use Illuminate\Http\Request;

class AdminBookingController extends Controller
{
    public function index()
    {
        $bookings = Booking::with(['user', 'room'])
            ->latest()
            ->paginate(10);

        return view('admin.bookings.index', compact('bookings'));
    }

    public function approve(Request $request, Booking $booking)
    {
        $booking->status = 'confirmed';
        $booking->admin_notes = $request->input('admin_notes');
        $booking->save();

        return redirect()->back()->with('success', 'Booking berhasil disetujui.');
    }

    public function reject(Request $request, Booking $booking)
    {
        $request->validate([
            'admin_notes' => 'required|string|max:255',
        ]);

        $booking->status = 'rejected';
        $booking->admin_notes = $request->admin_notes;
        $booking->save();

        return redirect()->back()->with('success', 'Booking berhasil ditolak.');
    }
}
