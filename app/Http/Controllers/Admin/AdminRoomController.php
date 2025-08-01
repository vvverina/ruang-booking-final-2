<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\Http\Request;

class AdminRoomController extends Controller
{
    public function index()
    {
        $rooms = Room::latest()->paginate(10);
        return view('admin.rooms.index', compact('rooms'));
    }

    public function create()
    {
        return view('admin.rooms.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'capacity' => 'required|integer',
            'location' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'facilities' => 'nullable|array',
        ]);

        $data = $request->only('name', 'capacity', 'location', 'description');
        $data['facilities'] = $request->input('facilities', []);
        $data['is_active'] = true;

        Room::create($data);

        return redirect()->route('admin.rooms.index')->with('success', 'Ruangan berhasil ditambahkan.');
    }

    public function edit(Room $room)
    {
        return view('admin.rooms.edit', compact('room'));
    }

    public function update(Request $request, Room $room)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'capacity' => 'required|integer',
            'location' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'facilities' => 'nullable|array',
        ]);

        $room->update($request->only('name', 'capacity', 'location', 'description'));
        $room->facilities = $request->input('facilities', []);
        $room->save();

        return redirect()->route('admin.rooms.index')->with('success', 'Ruangan berhasil diperbarui.');
    }

    public function toggleStatus(Room $room)
    {
        $room->is_active = ! $room->is_active;
        $room->save();

        return redirect()->back()->with('success', 'Status ruangan berhasil diubah.');
    }
}
