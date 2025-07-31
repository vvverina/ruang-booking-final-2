<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('bookings.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg transition duration-200">
                    <i class="fas fa-plus mr-2"></i>Booking Baru
                </a>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Welcome Section -->
        <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-xl p-6 text-white mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold mb-2">Selamat datang, {{ auth()->user()->name }}!</h1>
                    <p class="text-indigo-100">{{ ucfirst(auth()->user()->role) }} • {{ now()->translatedFormat('l, d F Y') }}</p>
                </div>
                <div class="text-right">
                    <i class="fas fa-user-circle text-6xl opacity-50"></i>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            @if(auth()->user()->isAdmin())
                <div class="bg-white rounded-xl shadow-md p-6 card-hover">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                            <i class="fas fa-door-open text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Total Ruangan</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['total_rooms'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-md p-6 card-hover">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-100 text-green-600">
                            <i class="fas fa-users text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Total User</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['total_users'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-md p-6 card-hover">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                            <i class="fas fa-clock text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Pending Approval</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['pending_bookings'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-md p-6 card-hover">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                            <i class="fas fa-calendar-check text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Booking Hari Ini</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['today_bookings'] }}</p>
                        </div>
                    </div>
                </div>
            @elseif(auth()->user()->isManager())
                <div class="bg-white rounded-xl shadow-md p-6 card-hover">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                            <i class="fas fa-door-open text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Ruangan Dikelola</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['managed_rooms'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-md p-6 card-hover">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                            <i class="fas fa-exclamation-circle text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Butuh Approval</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['pending_approvals'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-md p-6 card-hover">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-100 text-green-600">
                            <i class="fas fa-calendar-day text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Booking Hari Ini</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['today_bookings'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-md p-6 card-hover">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                            <i class="fas fa-calendar-week text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Minggu Ini</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['this_week_bookings'] }}</p>
                        </div>
                    </div>
                </div>
            @else
                <div class="bg-white rounded-xl shadow-md p-6 card-hover">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                            <i class="fas fa-calendar-alt text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Total Booking</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['my_bookings'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-md p-6 card-hover">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-100 text-green-600">
                            <i class="fas fa-arrow-up text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Akan Datang</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['upcoming_bookings'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-md p-6 card-hover">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                            <i class="fas fa-clock text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Menunggu Approval</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['pending_bookings'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-md p-6 card-hover">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                            <i class="fas fa-calendar-check text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Bulan Ini</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['this_month_bookings'] }}</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Recent Bookings -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-md p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-gray-900">
                            <i class="fas fa-history mr-2 text-indigo-600"></i>Booking Terbaru
                        </h3>
                        <a href="{{ route('bookings.index') }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                            Lihat Semua <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                    
                    @if($recentBookings->count() > 0)
                        <div class="space-y-4">
                            @foreach($recentBookings as $booking)
                                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition duration-200">
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1">
                                            <div class="flex items-center space-x-3">
                                                <span class="px-2 py-1 text-xs font-medium rounded-full status-{{ $booking->status }}">
                                                    {{ ucfirst($booking->status) }}
                                                </span>
                                                <span class="text-sm text-gray-500">{{ $booking->booking_code }}</span>
                                            </div>
                                            <h4 class="font-medium text-gray-900 mt-1">{{ $booking->title }}</h4>
                                            <p class="text-sm text-gray-600">
                                                <i class="fas fa-door-open mr-1"></i>{{ $booking->room->name }}
                                                <span class="mx-2">•</span>
                                                <i class="fas fa-calendar mr-1"></i>{{ $booking->booking_date->translatedFormat('d M Y') }}
                                                <span class="mx-2">•</span>
                                                <i class="fas fa-clock mr-1"></i>{{ $booking->start_time->format('H:i') }} - {{ $booking->end_time->format('H:i') }}
                                            </p>
                                            @if(!auth()->user()->isAdmin())
                                                <p class="text-sm text-gray-500 mt-1">
                                                    <i class="fas fa-user mr-1"></i>{{ $booking->user->name }}
                                                </p>
                                            @endif
                                        </div>
                                        <div class="flex space-x-2">
                                            <a href="{{ route('bookings.show', $booking) }}" class="text-indigo-600 hover:text-indigo-800">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <i class="fas fa-calendar-times text-4xl text-gray-300 mb-4"></i>
                            <p class="text-gray-500">Belum ada booking terbaru</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Upcoming Events -->
            <div>
                <div class="bg-white rounded-xl shadow-md p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-gray-900">
                            <i class="fas fa-calendar-day mr-2 text-green-600"></i>Jadwal Mendatang
                        </h3>
                    </div>
                    
                    @if($upcomingEvents->count() > 0)
                        <div class="space-y-3">
                            @foreach($upcomingEvents->take(5) as $event)
                                <div class="border-l-4 border-green-500 pl-4 py-2">
                                    <h4 class="font-medium text-gray-900 text-sm">{{ $event->title }}</h4>
                                    <p class="text-xs text-gray-600">
                                        <i class="fas fa-door-open mr-1"></i>{{ $event->room->name }}
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        <i class="fas fa-calendar mr-1"></i>{{ $event->booking_date->translatedFormat('d M') }}
                                        <span class="mx-1">•</span>
                                        <i class="fas fa-clock mr-1"></i>{{ $event->start_time->format('H:i') }}
                                    </p>
                                </div>
                            @endforeach
                        </div>
                        
                        @if($upcomingEvents->count() > 5)
                            <div class="mt-4 text-center">
                                <span class="text-sm text-gray-500">+{{ $upcomingEvents->count() - 5 }} jadwal lainnya</span>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-calendar-day text-3xl text-gray-300 mb-2"></i>
                            <p class="text-gray-500 text-sm">Tidak ada jadwal mendatang</p>
                        </div>
                    @endif
                </div>

                <!-- Quick Actions -->
                <div class="bg-white rounded-xl shadow-md p-6 mt-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        <i class="fas fa-bolt mr-2 text-yellow-600"></i>Aksi Cepat
                    </h3>
                    <div class="space-y-3">
                        <a href="{{ route('bookings.create') }}" class="block w-full bg-indigo-600 hover:bg-indigo-700 text-white text-center py-2 px-4 rounded-lg transition duration-200">
                            <i class="fas fa-plus mr-2"></i>Booking Baru
                        </a>
                        <a href="{{ route('rooms.index') }}" class="block w-full bg-green-600 hover:bg-green-700 text-white text-center py-2 px-4 rounded-lg transition duration-200">
                            <i class="fas fa-door-open mr-2"></i>Lihat Ruangan
                        </a>
                        <a href="{{ route('my-bookings.index') }}" class="block w-full bg-blue-600 hover:bg-blue-700 text-white text-center py-2 px-4 rounded-lg transition duration-200">
                            <i class="fas fa-list mr-2"></i>Booking Saya
                        </a>
                    </div>
                </div>

                @if(isset($roomUsage) && $roomUsage)
                    <!-- Room Usage Chart -->
                    <div class="bg-white rounded-xl shadow-md p-6 mt-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            <i class="fas fa-chart-bar mr-2 text-purple-600"></i>Penggunaan Ruangan
                        </h3>
                        <div class="space-y-3">
                            @foreach($roomUsage->take(5) as $room)
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600 truncate">{{ $room->name }}</span>
                                    <div class="flex items-center space-x-2">
                                        <div class="w-20 bg-gray-200 rounded-full h-2">
                                            <div class="bg-purple-600 h-2 rounded-full" style="width: {{ $room->bookings_count > 0 ? min(($room->bookings_count / $roomUsage->max('bookings_count')) * 100, 100) : 0 }}%"></div>
                                        </div>
                                        <span class="text-xs text-gray-500">{{ $room->bookings_count }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>