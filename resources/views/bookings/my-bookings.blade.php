
@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold mb-4">Booking Saya</h1>

    @if($bookings->isEmpty())
        <div class="bg-yellow-100 text-yellow-700 p-4 rounded">
            Belum ada booking yang Anda lakukan.
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-200 shadow-md rounded">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="py-2 px-4 border-b">Ruangan</th>
                        <th class="py-2 px-4 border-b">Tanggal</th>
                        <th class="py-2 px-4 border-b">Jam</th>
                        <th class="py-2 px-4 border-b">Status</th>
                        <th class="py-2 px-4 border-b">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bookings as $booking)
                        <tr>
                            <td class="py-2 px-4 border-b">{{ $booking->room->name }}</td>
                            <td class="py-2 px-4 border-b">{{ $booking->date }}</td>
                            <td class="py-2 px-4 border-b">{{ $booking->start_time }} - {{ $booking->end_time }}</td>
                            <td class="py-2 px-4 border-b">
                                <span class="px-2 py-1 rounded text-white {{ $booking->status == 'approved' ? 'bg-green-500' : ($booking->status == 'rejected' ? 'bg-red-500' : 'bg-yellow-500') }}">
                                    {{ ucfirst($booking->status) }}
                                </span>
                            </td>
                            <td class="py-2 px-4 border-b">
                                {{-- Tambahkan aksi jika diperlukan --}}
                                @if($booking->status == 'pending')
                                    <form action="{{ route('bookings.cancel', $booking->id) }}" method="POST" onsubmit="return confirm('Batalkan booking ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline">Batalkan</button>
                                    </form>
                                @else
                                    <span class="text-gray-500">-</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
