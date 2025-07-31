
@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto p-6">
    <h1 class="text-2xl font-bold mb-6">Laporan Ruangan</h1>

    <table class="min-w-full bg-white border border-gray-300 rounded-lg shadow-md">
        <thead class="bg-gray-100">
            <tr>
                <th class="py-3 px-4 text-left border-b">Nama Ruangan</th>
                <th class="py-3 px-4 text-left border-b">Jumlah Booking</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rooms as $room)
                <tr class="hover:bg-gray-50">
                    <td class="py-3 px-4 border-b">{{ $room->name }}</td>
                    <td class="py-3 px-4 border-b">{{ $room->bookings_count }}</td>
                </tr>
            @empty
                <tr>
                    <td class="py-3 px-4 border-b text-center" colspan="2">Tidak ada data ruangan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
