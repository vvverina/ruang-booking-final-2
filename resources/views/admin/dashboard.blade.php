@extends('admin.layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-1">Admin Dashboard</h1>
                    <p class="text-muted">Panel administrasi sistem booking ruangan</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.rooms.create') }}" class="btn btn-outline-primary">
                        <i class="bi bi-plus-circle me-2"></i>
                        Tambah Ruangan
                    </a>
                    <a href="{{ route('admin.bookings') }}" class="btn btn-primary">
                        <i class="bi bi-calendar-check me-2"></i>
                        Kelola Booking
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-1 opacity-75">Total Booking</h6>
                        <h3 class="mb-0">{{ $stats['total_bookings'] }}</h3>
                    </div>
                    <i class="bi bi-calendar3 fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <div class="stats-card warning">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-1 opacity-75">Pending</h6>
                        <h3 class="mb-0">{{ $stats['pending_bookings'] }}</h3>
                    </div>
                    <i class="bi bi-clock fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <div class="stats-card success">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-1 opacity-75">Hari Ini</h6>
                        <h3 class="mb-0">{{ $stats['today_bookings'] }}</h3>
                    </div>
                    <i class="bi bi-calendar-day fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <div class="stats-card danger">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-1 opacity-75">Total Ruangan</h6>
                        <h3 class="mb-0">{{ $stats['total_rooms'] }}</h3>
                    </div>
                    <i class="bi bi-door-open fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
        
        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <div class="stats-card warning">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-1 opacity-75">Total User</h6>
                        <h3 class="mb-0">{{ $stats['total_users'] }}</h3>
                    </div>
                    <i class="bi bi-people fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Pending Bookings -->
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-clock me-2"></i>
                        Booking Terbaru
                    </h5>
                    <a href="{{ route('admin.bookings') }}" class="text-decoration-none">
                        Lihat Semua <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
                <div class="card-body">
                    @forelse($recentBookings as $booking)
                        <div class="d-flex justify-content-between align-items-start mb-3 pb-3 border-bottom last:border-0 last:pb-0 last:mb-0">
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start mb-1">
                                    <h6 class="mb-0">{{ $booking->room->name }}</h6>
                                    <span class="{{ $booking->status_badge_class }}">
                                        {{ ucfirst($booking->status) }}
                                    </span>
                                </div>
                                <p class="text-muted mb-1">
                                    <i class="bi bi-person me-1"></i>
                                    {{ optional($booking->user)->name }}
                                </p>
                                <div class="row">
                                    <div class="col-6">
                                        <small class="text-muted">
                                            <i class="bi bi-calendar me-1"></i>
                                            {{ $booking->booking_date->format('d M') }}
                                        </small>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted">
                                            <i class="bi bi-clock me-1"></i>
                                            {{ $booking->start_time->format('H:i') }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-3">
                            <i class="bi bi-calendar-x fs-1 text-muted"></i>
                            <p class="text-muted mt-2 mb-0">Belum ada booking</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection