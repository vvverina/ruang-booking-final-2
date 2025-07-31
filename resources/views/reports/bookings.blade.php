@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-6">
    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
        <h2 class="text-2xl font-bold mb-4">Laporan Booking</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-200">
                <thead>
                    <tr class="bg-gray-100 text-left text-sm font-medium text-gray-700">
                        <th class="px-4 py-2 border">#</th>
                        <th class="px-4 py-2 border">Ruangan</th>
                        <th class="px-4 py-2 border">User</th>
                        <th class="px-4 py-2 border">Tanggal</th>
                        <th class="px-4 py-2 border">Jam</th>
                        <th class="px-4 py-2 border">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bookings as $booking)
                        <tr class="text-sm text-gray-900">
                            <td class="px-4 py-2 border">{{ $loop->iteration }}</td>
                            <td class="px-4 py-2 border">{{ $booking->room->name ?? '-' }}</td>
                            <td class="px-4 py-2 border">{{ $booking->user->name ?? '-' }}</td>
                            <td class="px-4 py-2 border">{{ $booking->date }}</td>
                            <td class="px-4 py-2 border">{{ $booking->start_time }} - {{ $booking->end_time }}</td>
                            <td class="px-4 py-2 border">
                                <span class="px-2 py-1 rounded text-white text-xs @if($booking->status == 'confirmed') bg-green-500 @elseif($booking->status == 'pending') bg-yellow-500 @else bg-red-500 @endif">
                                    {{ ucfirst($booking->status) }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection