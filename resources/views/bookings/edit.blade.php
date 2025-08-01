@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('bookings.index') }}">Booking Saya</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('bookings.show', $booking) }}">Detail</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-pencil me-2"></i>
                        Edit Booking
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('bookings.update', $booking) }}" method="POST" id="bookingForm">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="room_id" class="form-label">Ruangan <span class="text-danger">*</span></label>
                                <select name="room_id" id="room_id" class="form-select @error('room_id') is-invalid @enderror" required>
                                    <option value="">Pilih Ruangan</option>
                                    @foreach($rooms as $room)
                                        <option value="{{ $room->id }}" 
                                                {{ old('room_id', $booking->room_id) == $room->id ? 'selected' : '' }}
                                                data-capacity="{{ $room->capacity }}"
                                                data-location="{{ $room->location }}"
                                                                                                data-location="{{ $room->location }}">
                                            {{ $room->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('room_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="booking_date" class="form-label">Tanggal Booking <span class="text-danger">*</span></label>
                                <input type="date" 
                                       name="booking_date" 
                                       id="booking_date" 
                                       class="form-control @error('booking_date') is-invalid @enderror" 
                                       value="{{ old('booking_date', $booking->booking_date->format('Y-m-d')) }}" 
                                       required>
                                @error('booking_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="start_time" class="form-label">Waktu Mulai <span class="text-danger">*</span></label>
                                <input type="time" 
                                       name="start_time" 
                                       id="start_time" 
                                       class="form-control @error('start_time') is-invalid @enderror" 
                                       value="{{ old('start_time', $booking->start_time->format('H:i')) }}" 
                                       required>
                                @error('start_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="end_time" class="form-label">Waktu Selesai <span class="text-danger">*</span></label>
                                <input type="time" 
                                       name="end_time" 
                                       id="end_time" 
                                       class="form-control @error('end_time') is-invalid @enderror" 
                                       value="{{ old('end_time', $booking->end_time->format('H:i')) }}" 
                                       required>
                                @error('end_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12 mb-3">
                                <label for="purpose" class="form-label">Keperluan Booking <span class="text-danger">*</span></label>
                                <textarea name="purpose" 
                                          id="purpose" 
                                          class="form-control @error('purpose') is-invalid @enderror" 
                                          rows="3" 
                                          required>{{ old('purpose', $booking->purpose) }}</textarea>
                                @error('purpose')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('bookings.show', $booking) }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-1"></i>
                                Batal
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-1"></i>
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection