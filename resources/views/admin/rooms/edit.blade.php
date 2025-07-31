<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Ruangan') }}: {{ $room->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST" action="{{ route('admin.rooms.update', $room) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <!-- Room Image Preview -->
                        <div class="mb-6">
                            <x-input-label :value="__('Gambar Saat Ini')" />
                            <img src="{{ $room->image_url ?? 'https://placehold.co/600x400?text=No+Image' }}" alt="{{ $room->name }}" class="mt-2 h-48 object-cover rounded-lg">
                        </div>

                        <!-- Basic Information -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <x-input-label for="name" :value="__('Nama Ruangan*')" />
                                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $room->name)" required autofocus />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="code" :value="__('Kode Ruangan*')" />
                                <x-text-input id="code" class="block mt-1 w-full" type="text" name="code" :value="old('code', $room->code)" required />
                                <x-input-error :messages="$errors->get('code')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Room Details -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                            <div>
                                <x-input-label for="capacity" :value="__('Kapasitas*')" />
                                <x-text-input id="capacity" class="block mt-1 w-full" type="number" name="capacity" :value="old('capacity', $room->capacity)" required />
                                <x-input-error :messages="$errors->get('capacity')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="price_per_hour" :value="__('Harga per Jam*')" />
                                <div class="relative mt-1 rounded-md shadow-sm">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                        <span class="text-gray-500 sm:text-sm">Rp</span>
                                    </div>
                                    <input type="text" name="price_per_hour" id="price_per_hour" class="block w-full rounded-md border-gray-300 pl-9 pr-12 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" value="{{ old('price_per_hour', $room->price_per_hour) }}">
                                </div>
                                <x-input-error :messages="$errors->get('price_per_hour')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="floor" :value="__('Lantai*')" />
                                <x-text-input id="floor" class="block mt-1 w-full" type="number" name="floor" :value="old('floor', $room->floor)" required />
                                <x-input-error :messages="$errors->get('floor')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Location and Image -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <x-input-label for="location" :value="__('Lokasi*')" />
                                <x-text-input id="location" class="block mt-1 w-full" type="text" name="location" :value="old('location', $room->location)" required />
                                <x-input-error :messages="$errors->get('location')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="image" :value="__('Gambar Baru')" />
                                <input class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400" id="image" type="file" name="image">
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-300">Biarkan kosong jika tidak ingin mengganti gambar</p>
                                <x-input-error :messages="$errors->get('image')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Facilities -->
                        <div class="mb-6">
                            <x-input-label for="facilities" :value="__('Fasilitas')" />
                            <div class="mt-2 grid grid-cols-2 md:grid-cols-4 gap-2">
                                @foreach(['Proyektor', 'AC', 'Whiteboard', 'Sound System', 'Koneksi Internet', 'Kursi Ergonomis', 'Meja Rapat', 'LED TV'] as $facility)
                                    <div class="flex items-center">
                                        <input id="facility-{{ Str::slug($facility) }}" name="facilities[]" type="checkbox" value="{{ $facility }}" 
                                            {{ in_array($facility, old('facilities', $room->facilities ?? [])) ? 'checked' : '' }}
                                            class="w-4 h-4 text-blue-600 bg-gray-100 rounded border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                        <label for="facility-{{ Str::slug($facility) }}" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">{{ $facility }}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="mb-6">
                            <x-input-label for="description" :value="__('Deskripsi')" />
                            <textarea id="description" name="description" rows="3" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ old('description', $room->description) }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <!-- Availability -->
                        <div class="mb-6">
                            <div class="flex items-center">
                                <input id="is_available" name="is_available" type="checkbox" class="w-4 h-4 text-blue-600 bg-gray-100 rounded border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                                    {{ old('is_available', $room->is_available) ? 'checked' : '' }}>
                                <label for="is_available" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">Ruangan Tersedia</label>
                            </div>
                        </div>

                        <div class="flex justify-end">
                            <a href="{{ route('admin.rooms.index') }}" class="mr-4 px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-md hover:bg-gray-300 dark:hover:bg-gray-600">
                                Batal
                            </a>
                            <x-primary-button>
                                Simpan Perubahan
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>