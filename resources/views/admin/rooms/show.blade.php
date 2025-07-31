<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Detail Ruangan') }}: {{ $room->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex flex-col md:flex-row gap-8">
                        <!-- Room Image -->
                        <div class="md:w-1/3">
                            <img src="{{ $room->image_url ?? 'https://placehold.co/600x400' }}" alt="{{ $room->name }}" class="w-full rounded-lg shadow-md">
                        </div>
                        
                        <!-- Room Details -->
                        <div class="md:w-2/3">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h3 class="text-2xl font-bold">{{ $room->name }} ({{ $room->code }})</h3>
                                    <p class="text-gray-600 dark:text-gray-400 mt-2">
                                        {{ $room->location }}, Lantai {{ $room->floor }}
                                    </p>
                                </div>
                                <span class="px-3 py-1 rounded-full text-sm font-medium {{ $room->is_available ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                                    {{ $room->is_available ? 'Available' : 'Unavailable' }}
                                </span>
                            </div>

                            <hr class="my-4 border-gray-200 dark:border-gray-700">

                            <!-- Room Specifications -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-gray-500 dark:text-gray-400">Kapasitas:</p>
                                    <p class="font-medium">{{ $room->capacity }} orang</p>
                                </div>
                                <div>
                                    <p class="text-gray-500 dark:text-gray-400">Harga per Jam:</p>
                                    <p class="font-medium">Rp{{ number_format($room->price_per_hour) }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-500 dark:text-gray-400">Fasilitas:</p>
                                    <div class="flex flex-wrap gap-2 mt-1">
                                        @foreach($room->facilities as $facility)
                                            <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                                {{ $facility }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="mt-6">
                                <p class="text-gray-500 dark:text-gray-400 mb-2">Deskripsi:</p>
                                <p class="text-gray-700 dark:text-gray-300">{{ $room->description ?? 'Tidak ada deskripsi' }}</p>
                            </div>

                            <!-- Statistics -->
                            <div class="mt-8">
                                <h4 class="font-medium text-lg mb-3">Statistik Penggunaan</h4>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div class="bg-blue-50 dark:bg-gray-700 p-4 rounded-lg">
                                        <p class="text-blue-800 dark:text-blue-400 text-sm">Total Booking</p>
                                        <p class="text-2xl font-bold">{{ $room->bookings_count }}</p>
                                    </div>
                                    <div class="bg-green-50 dark:bg-gray-700 p-4 rounded-lg">
                                        <p class="text-green-800 dark:text-green-400 text-sm">Rating</p>
                                        <div class="flex items-center">
                                            @php $rating = $room->average_rating; @endphp
                                            @for($i = 1; $i <= 5; $i++)
                                                @if($i <= floor($rating))
                                                    <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                    </svg>
                                                @elseif($i == ceil($rating) && $rating != floor($rating))
                                                    <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M10 14.667L4.96 17.12l.829-4.833-3.497-3.42 4.83-.703L10 4.667l2.878 3.497 4.83.702-3.496 3.42.828 4.833-5.04-1.453z" fill="url(#half-star)"></path>
                                                        <defs>
                                                            <linearGradient id="half-star" x1="0" y1="0" x2="100%" y2="0">
                                                                <stop offset="50%" stop-color="currentColor"></stop>
                                                                <stop offset="50%" stop-color="rgb(209, 213, 219)" stop-opacity="1"></stop>
                                                            </linearGradient>
                                                        </defs>
                                                    </svg>
                                                @else
                                                    <svg class="w-5 h-5 text-gray-300 dark:text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                    </svg>
                                                @endif
                                            @endfor
                                            <span class="ml-1 font-medium">{{ number_format($rating, 1) }}/5</span>
                                        </div>
                                    </div>
                                    <div class="bg-purple-50 dark:bg-gray-700 p-4 rounded-lg">
                                        <p class="text-purple-800 dark:text-purple-400 text-sm">Penggunaan Bulan Ini</p>
                                        <p class="text-2xl font-bold">{{ $room->bookings_this_month_count }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Booking History -->
                            <div class="mt-8">
                                <h4 class="font-medium text-lg mb-3">Riwayat Booking</h4>
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                        <thead class="bg-gray-50 dark:bg-gray-700">
                                            <tr>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Tanggal</th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Pemesan</th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                            @foreach($room->bookings->take(5) as $booking)
                                                <tr>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $booking->booking_date->format('d M Y') }}</td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $booking->user->name }}</td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                        {!! $booking->status_badge !!}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                        <a href="{{ route('admin.bookings.show', $booking) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-600">Detail</a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @if($room->bookings->count() > 5)
                                    <div class="mt-4 text-center">
                                        <a href="{{ route('admin.bookings.index', ['room_id' => $room->id]) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-sm">
                                            Lihat semua booking ({{ $room->bookings->count() }})
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="mt-6 flex justify-end">
                        <a href="{{ route('admin.rooms.index') }}" class="mr-4 px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-md hover:bg-gray-300 dark:hover:bg-gray-600">
                            Kembali
                        </a>
                        <a href="{{ route('admin.rooms.edit', $room) }}" class="mr-4 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                            Edit
                        </a>
                        <form method="POST" action="{{ route('admin.rooms.destroy', $room) }}" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="button" onclick="confirmDelete('{{ $room->name }}', this.form)" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                                Hapus
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function confirmDelete(roomName, form) {
                Swal.fire({
                    title: `Hapus ${roomName}?`,
                    text: "Anda tidak akan dapat mengembalikan data ini!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            }
        </script>
    @endpush
</x-app-layout>