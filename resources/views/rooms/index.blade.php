<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <i class="fas fa-door-open mr-2"></i>Daftar Ruangan
            </h2>
            @if(auth()->user()->isAdmin())
                <a href="{{ route('admin.rooms.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg transition duration-200">
                    <i class="fas fa-plus mr-2"></i>Tambah Ruangan
                </a>
            @endif
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Filter Section -->
        <div class="bg-white rounded-xl shadow-md p-6 mb-8">
            <form method="GET" action="{{ route('rooms.index') }}" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label for="capacity" class="block text-sm font-medium text-gray-700 mb-1">Kapasitas Minimum</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-users text-gray-400"></i>
                            </div>
                            <input type="number" id="capacity" name="capacity" value="{{ request('capacity') }}" 
                                   class="pl-10 block w-full border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" 
                                   placeholder="Jumlah orang">
                        </div>
                    </div>

                    <div>
                        <label for="location" class="block text-sm font-medium text-gray-700 mb-1">Lokasi</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-map-marker-alt text-gray-400"></i>
                            </div>
                            <select id="location" name="location" class="pl-10 block w-full border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Semua Lokasi</option>
                                @foreach($locations as $location)
                                    <option value="{{ $location }}" {{ request('location') == $location ? 'selected' : '' }}>
                                        {{ $location }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label for="facilities" class="block text-sm font-medium text-gray-700 mb-1">Fasilitas</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-tv text-gray-400"></i>
                            </div>
                            <select id="facilities" name="facilities[]" multiple class="pl-10 block w-full border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                                @foreach($allFacilities as $facility)
                                    <option value="{{ $facility }}" {{ in_array($facility, request('facilities', [])) ? 'selected' : '' }}>
                                        {{ $facility }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="flex items-end space-x-2">
                        <button type="submit" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg transition duration-200">
                            <i class="fas fa-search mr-2"></i>Filter
                        </button>
                        <a href="{{ route('rooms.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-lg transition duration-200">
                            <i class="fas fa-undo"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Room Grid -->
        @if($rooms->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                @foreach($rooms as $room)
                    <div class="bg-white rounded-xl shadow-md overflow-hidden card-hover">
                        <!-- Room Image -->
                        <div class="h-48 bg-gray-300 relative">
                            @if($room->image)
                                <img src="{{ $room->image_url }}" alt="{{ $room->name }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-indigo-400 to-purple-500">
                                    <i class="fas fa-door-open text-6xl text-white opacity-50"></i>
                                </div>
                            @endif
                            
                            <!-- Status Badge -->
                            <div class="absolute top-3 right-3">
                                @if($room->status === 'available')
                                    <span class="bg-green-100 text-green-800 text-xs font-medium px-2 py-1 rounded-full">
                                        <i class="fas fa-check-circle mr-1"></i>Tersedia
                                    </span>
                                @elseif($room->status === 'maintenance')
                                    <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2 py-1 rounded-full">
                                        <i class="fas fa-tools mr-1"></i>Maintenance
                                    </span>
                                @else
                                    <span class="bg-red-100 text-red-800 text-xs font-medium px-2 py-1 rounded-full">
                                        <i class="fas fa-times-circle mr-1"></i>Tidak Tersedia
                                    </span>
                                @endif
                            </div>

                            <!-- Price Badge -->
                            @if($room->price_per_hour > 0)
                                <div class="absolute top-3 left-3">
                                    <span class="bg-black bg-opacity-50 text-white text-xs font-medium px-2 py-1 rounded-full">
                                        Rp {{ number_format($room->price_per_hour, 0, ',', '.') }}/jam
                                    </span>
                                </div>
                            @endif
                        </div>

                        <!-- Room Info -->
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-2">
                                <h3 class="text-lg font-semibold text-gray-900">{{ $room->name }}</h3>
                                <span class="text-sm text-gray-500">{{ $room->code }}</span>
                            </div>
                            
                            <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ $room->description }}</p>
                            
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div class="flex items-center text-sm text-gray-600">
                                    <i class="fas fa-users mr-2 text-indigo-600"></i>
                                    <span>{{ $room->capacity }} orang</span>
                                </div>
                                <div class="flex items-center text-sm text-gray-600">
                                    <i class="fas fa-map-marker-alt mr-2 text-indigo-600"></i>
                                    <span>{{ $room->location }}</span>
                                </div>
                                @if($room->floor)
                                    <div class="flex items-center text-sm text-gray-600">
                                        <i class="fas fa-layer-group mr-2 text-indigo-600"></i>
                                        <span>Lantai {{ $room->floor }}</span>
                                    </div>
                                @endif
                                @if($room->requires_approval)
                                    <div class="flex items-center text-sm text-gray-600">
                                        <i class="fas fa-shield-alt mr-2 text-yellow-600"></i>
                                        <span>Perlu Approval</span>
                                    </div>
                                @endif
                            </div>

                            <!-- Facilities -->
                            @if($room->facilities && count($room->facilities) > 0)
                                <div class="mb-4">
                                    <p class="text-sm font-medium text-gray-700 mb-2">Fasilitas:</p>
                                    <div class="flex flex-wrap gap-1">
                                        @foreach(array_slice($room->facilities, 0, 3) as $facility)
                                            <span class="bg-indigo-100 text-indigo-800 text-xs px-2 py-1 rounded-full">
                                                {{ $facility }}
                                            </span>
                                        @endforeach
                                        @if(count($room->facilities) > 3)
                                            <span class="text-xs text-gray-500">+{{ count($room->facilities) - 3 }} lainnya</span>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            <!-- Action Buttons -->
                            <div class="flex space-x-2">
                                <a href="{{ route('rooms.show', $room) }}" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white text-center py-2 px-4 rounded-lg transition duration-200 text-sm">
                                    <i class="fas fa-eye mr-2"></i>Detail
                                </a>
                                @if($room->status === 'available')
                                    <a href="{{ route('bookings.create', ['room_id' => $room->id]) }}" class="flex-1 bg-green-600 hover:bg-green-700 text-white text-center py-2 px-4 rounded-lg transition duration-200 text-sm">
                                        <i class="fas fa-calendar-plus mr-2"></i>Booking
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="bg-white rounded-xl shadow-md p-6">
                {{ $rooms->links() }}
            </div>
        @else
            <div class="bg-white rounded-xl shadow-md p-12 text-center">
                <i class="fas fa-search text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-xl font-medium text-gray-900 mb-2">Tidak ada ruangan ditemukan</h3>
                <p class="text-gray-600 mb-6">Coba ubah filter pencarian atau hapus beberapa kriteria</p>
                <a href="{{ route('rooms.index') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-lg transition duration-200">
                    <i class="fas fa-undo mr-2"></i>Reset Filter
                </a>
            </div>
        @endif
    </div>
</x-app-layout>