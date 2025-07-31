<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex items-center space-x-4">
                <a href="{{ route('rooms.index') }}" class="text-gray-600 hover:text-gray-800">
                    <i class="fas fa-arrow-left text-xl"></i>
                </a>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ $room->name }}
                </h2>
                <span class="px-3 py-1 text-sm font-medium rounded-full {{ $room->status === 'available' ? 'bg-green-100 text-green-800' : ($room->status === 'maintenance' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                    {{ ucfirst($room->status) }}
                </span>
            </div>
            <div class="flex space-x-2">
                @if($room->status === 'available')
                    <a href="{{ route('bookings.create', ['room_id' => $room->id]) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg transition duration-200">
                        <i class="fas fa-calendar-plus mr-2"></i>Booking Sekarang
                    </a>
                @endif
                <a href="{{ route('rooms.calendar', $room) }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition duration-200">
                    <i class="fas fa-calendar-alt mr-2"></i>Lihat Kalendar
                </a>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Room Details -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Room Image and Basic Info -->
                <div class="bg-white rounded-xl shadow-md overflow-hidden">
                    <div class="h-64 bg-gray-300 relative">
                        @if($room->image)
                            <img src="{{ $room->image_url }}" alt="{{ $room->name }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-indigo-400 to-purple-500">
                                <i class="fas fa-door-open text-8xl text-white opacity-50"></i>
                            </div>
                        @endif
                        
                        @if($room->price_per_hour > 0)
                            <div class="absolute top-4 left-4">
                                <span class="bg-black bg-opacity-70 text-white px-3 py-2 rounded-lg font-medium">
                                    Rp {{ number_format($room->price_per_hour, 0, ',', '.') }}/jam
                                </span>
                            </div>
                        @endif
                    </div>
                    
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h1 class="text-2xl font-bold text-gray-900 mb-2">{{ $room->name }}</h1>
                                <p class="text-gray-600">Kode: {{ $room->code }}</p>
                            </div>
                        </div>
                        
                        @if($room->description)
                            <p class="text-gray-700 mb-6">{{ $room->description }}</p>
                        @endif
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                            <div class="flex items-center space-x-3">
                                <div class="p-3 bg-indigo-100 rounded-full">
                                    <i class="fas fa-users text-indigo-600"></i>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Kapasitas</p>
                                    <p class="font-semibold text-gray-900">{{ $room->capacity }} orang</p>
                                </div>
                            </div>
                            
                            <div class="flex items-center space-x-3">
                                <div class="p-3 bg-green-100 rounded-full">
                                    <i class="fas fa-map-marker-alt text-green-600"></i>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Lokasi</p>
                                    <p class="font-semibold text-gray-900">{{ $room->location }}</p>
                                </div>
                            </div>
                            
                            @if($room->floor)
                                <div class="flex items-center space-x-3">
                                    <div class="p-3 bg-purple-100 rounded-full">
                                        <i class="fas fa-layer-group text-purple-600"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Lantai</p>
                                        <p class="font-semibold text-gray-900">{{ $room->floor }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                        
                        @if($room->requires_approval)
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                                <div class="flex items-center">
                                    <i class="fas fa-shield-alt text-yellow-600 mr-3"></i>
                                    <div>
                                        <h4 class="font-medium text-yellow-800">Memerlukan Persetujuan</h4>
                                        <p class="text-yellow-700 text-sm">Booking ruangan ini memerlukan persetujuan dari manager sebelum dikonfirmasi.</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                        
                        <!-- Facilities -->
                        @if($room->facilities && count($room->facilities) > 0)
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-3">Fasilitas Tersedia</h3>
                                <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                                    @foreach($room->facilities as $facility)
                                        <div class="flex items-center space-x-2 bg-gray-50 rounded-lg p-3">
                                            <i class="fas fa-check-circle text-green-500"></i>
                                            <span class="text-gray-700">{{ $facility }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Today's Schedule -->
                <div class="bg-white rounded-xl shadow-md p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-gray-900">
                            <i class="fas fa-calendar-day mr-2 text-indigo-600"></i>Jadwal Hari Ini
                        </h3>
                        <span class="text-sm text-gray-500">{{ now()->translatedFormat('l, d F Y') }}</span>
                    </div>
                    
                    @if($todayBookings->count() > 0)
                        <div class="space-y-3">
                            @foreach($todayBookings as $booking)
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1">
                                            <div class="flex items-center space-x-3 mb-2">
                                                <span class="px-2 py-1 text-xs font-medium rounded-full status-{{ $booking->status }}">
                                                    {{ ucfirst($booking->status) }}
                                                </span>
                                                <span class="text-sm font-medium text-gray-900">{{ $booking->title }}</span>
                                            </div>
                                            <div class="flex items-center space-x-4 text-sm text-gray-600">
                                                <span>
                                                    <i class="fas fa-clock mr-1"></i>
                                                    {{ $booking->start_time->format('H:i') }} - {{ $booking->end_time->format('H:i') }}
                                                </span>
                                                <span>
                                                    <i class="fas fa-users mr-1"></i>
                                                    {{ $booking->participant_count }} orang
                                                </span>
                                                <span>
                                                    <i class="fas fa-user mr-1"></i>
                                                    {{ $booking->user->name }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <i class="fas fa-calendar-times text-4xl text-gray-300 mb-4"></i>
                            <p class="text-gray-500">Tidak ada booking hari ini</p>
                            @if($room->status === 'available')
                                <a href="{{ route('bookings.create', ['room_id' => $room->id]) }}" class="inline-block mt-4 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg transition duration-200">
                                    <i class="fas fa-plus mr-2"></i>Booking Sekarang
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Quick Booking -->
                @if($room->status === 'available')
                    <div class="bg-white rounded-xl shadow-md p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            <i class="fas fa-lightning-bolt mr-2 text-yellow-600"></i>Quick Booking
                        </h3>
                        <form action="{{ route('bookings.create') }}" method="GET">
                            <input type="hidden" name="room_id" value="{{ $room->id }}">
                            
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
                                    <input type="date" name="booking_date" value="{{ today()->toDateString() }}" min="{{ today()->toDateString() }}" 
                                           class="w-full border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                                </div>
                                
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Mulai</label>
                                        <input type="time" name="start_time" value="{{ now()->format('H:i') }}" 
                                               class="w-full border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Selesai</label>
                                        <input type="time" name="end_time" value="{{ now()->addHour()->format('H:i') }}" 
                                               class="w-full border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>
                                </div>
                                
                                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-2 px-4 rounded-lg transition duration-200">
                                    <i class="fas fa-calendar-plus mr-2"></i>Lanjut Booking
                                </button>
                            </div>
                        </form>
                    </div>
                @endif

                <!-- Weekly Schedule Preview -->
                <div class="bg-white rounded-xl shadow-md p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">
                            <i class="fas fa-calendar-week mr-2 text-green-600"></i>Jadwal Minggu Ini
                        </h3>
                        <a href="{{ route('rooms.calendar', $room) }}" class="text-indigo-600 hover:text-indigo-800 text-sm">
                            Lihat Semua
                        </a>
                    </div>
                    
                    @if($weekBookings->count() > 0)
                        <div class="space-y-2">
                            @foreach($weekBookings->take(5) as $booking)
                                <div class="text-sm border-l-4 border-indigo-500 pl-3 py-2">
                                    <p class="font-medium text-gray-900">{{ $booking->title }}</p>
                                    <p class="text-gray-600">
                                        {{ $booking->booking_date->translatedFormat('D, d M') }} • 
                                        {{ $booking->start_time->format('H:i') }}-{{ $booking->end_time->format('H:i') }}
                                    </p>
                                    <p class="text-gray-500">{{ $booking->user->name }}</p>
                                </div>
                            @endforeach
                            
                            @if($weekBookings->count() > 5)
                                <p class="text-center text-sm text-gray-500 pt-2">
                                    +{{ $weekBookings->count() - 5 }} booking lainnya
                                </p>
                            @endif
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-calendar text-3xl text-gray-300 mb-2"></i>
                            <p class="text-gray-500 text-sm">Tidak ada booking minggu ini</p>
                        </div>
                    @endif
                </div>

                <!-- Room Stats -->
                <div class="bg-white rounded-xl shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        <i class="fas fa-chart-bar mr-2 text-purple-600"></i>Statistik Ruangan
                    </h3>
                    
                    @php
                        $thisMonthBookings = $room->bookings()->whereMonth('booking_date', now()->month)->count();
                        $todayBookings = $room->bookings()->whereDate('booking_date', today())->count();
                        $upcomingBookings = $room->bookings()->where('booking_date', '>', today())->where('status', '!=', 'cancelled')->count();
                    @endphp
                    
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Bulan ini</span>
                            <span class="font-semibold text-gray-900">{{ $thisMonthBookings }} booking</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Hari ini</span>
                            <span class="font-semibold text-gray-900">{{ $todayBookings }} booking</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Akan datang</span>
                            <span class="font-semibold text-gray-900">{{ $upcomingBookings }} booking</span>
                        </div>
                    </div>
                </div>

                @if(auth()->user()->isAdmin())
                    <!-- Admin Actions -->
                    <div class="bg-white rounded-xl shadow-md p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            <i class="fas fa-cogs mr-2 text-red-600"></i>Admin Actions
                        </h3>
                        <div class="space-y-2">
                            <a href="{{ route('admin.rooms.edit', $room) }}" class="block w-full bg-blue-600 hover:bg-blue-700 text-white text-center py-2 px-4 rounded-lg transition duration-200 text-sm">
                                <i class="fas fa-edit mr-2"></i>Edit Ruangan
                            </a>
                            <a href="{{ route('admin.rooms.managers', $room) }}" class="block w-full bg-green-600 hover:bg-green-700 text-white text-center py-2 px-4 rounded-lg transition duration-200 text-sm">
                                <i class="fas fa-users-cog mr-2"></i>Kelola Manager
                            </a>
                            <form action="{{ route('admin.rooms.toggle-status', $room) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="w-full bg-yellow-600 hover:bg-yellow-700 text-white py-2 px-4 rounded-lg transition duration-200 text-sm">
                                    <i class="fas fa-toggle-on mr-2"></i>Toggle Status
                                </button>
                            </form>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>