@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-1">Kelola Booking</h1>
                    <p class="text-muted">Daftar semua booking ruangan</p>
                </div>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-primary">
                    <i class="bi bi-arrow-left me-2"></i>
                    Kembali ke Dashboard
                </a>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label for="status" class="form-label">Status</label>
                    <select name="status" id="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="room_id" class="form-label">Ruangan</label>
                    <select name="room_id" id="room_id" class="form-select">
                        <option value="">Semua Ruangan</option>
                        @foreach($rooms as $room)
                            <option value="{{ $room->id }}" {{ request('room_id') == $room->id ? 'selected' : '' }}>
                                {{ $room->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="date_from" class="form-label">Dari Tanggal</label>
                    <input type="date" name="date_from" id="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2">
                    <label for="date_to" class="form-label">Sampai Tanggal</label>
                    <input type="date" name="date_to" id="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <div class="d-flex gap-2 w-100">
                        <button type="submit" class="btn btn-primary flex-fill">
                            <i class="bi bi-search"></i>
                        </button>
                        <a href="{{ route('admin.bookings') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Bookings List -->
    <div class="card">
        <div class="card-header bg-white">
            <h6 class="card-title mb-0">
                Daftar Booking 
                <span class="badge bg-primary">{{ $bookings->total() }}</span>
            </h6>
        </div>
        <div class="card-body p-0">
            @forelse($bookings as $booking)
                <div class="border-bottom p-3">
                    <div class="row align-items-center">
                        <div class="col-md-3">
                            <h6 class="mb-1">{{ $booking->room->name }}</h6>
                            <small class="text-muted">
                                <i class="bi bi-person me-1"></i>
                                {{ $booking->user->name }}
                            </small>
                        </div>
                        <div class="col-md-2">
                            <small class="text-muted d-block">Tanggal</small>
                            <span>{{ $booking->booking_date->format('d M Y') }}</span>
                        </div>
                        <div class="col-md-2">
                            <small class="text-muted d-block">Waktu</small>
                            <span>{{ $booking->start_time->format('H:i') }} - {{ $booking->end_time->format('H:i') }}</span>
                        </div>
                        <div class="col-md-2">
                            <small class="text-muted d-block">Status</small>
                            <span class="{{ $booking->status_badge_class }}">
                                {{ ucfirst($booking->status) }}
                            </span>
                        </div>
                        <div class="col-md-3 text-end">
                            @if($booking->status === 'pending')
                                <div class="btn-group btn-group-sm">
                                    <form action="{{ route('admin.bookings.approve', $booking) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-success btn-sm" title="Setujui">
                                            <i class="bi bi-check"></i>
                                        </button>
                                    </form>
                                    <button type="button" 
                                            class="btn btn-danger btn-sm" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#rejectModal{{ $booking->id }}"
                                            title="Tolak">
                                        <i class="bi bi-x"></i>
                                    </button>
                                </div>
                            @endif
                            <a href="{{ route('bookings.show', $booking) }}" class="btn btn-outline-primary btn-sm ms-1" title="Detail">
                                <i class="bi bi-eye"></i>
                            </a>
                        </div>
                    </div>
                    
                    @if($booking->purpose)
                        <div class="mt-2">
                            <small class="text-muted">Tujuan: </small>
                            <small>{{ Str::limit($booking->purpose, 100) }}</small>
                        </div>
                    @endif

                    @if($booking->admin_notes)
                        <div class="mt-2">
                            <small class="text-muted">Catatan Admin: </small>
                            <small class="text-info">{{ $booking->admin_notes }}</small>
                        </div>
                    @endif
                </div>

                <!-- Reject Modal -->
                @if($booking->status === 'pending')
                    <div class="modal fade" id="rejectModal{{ $booking->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Tolak Booking</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <form action="{{ route('admin.bookings.reject', $booking) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <strong>Booking Details:</strong>
                                            <ul class="list-unstyled mt-2">
                                                <li>Ruangan: {{ $booking->room->name }}</li>
                                                <li>User: {{ $booking->user->name }}</li>
                                                <li>Tanggal: {{ $booking->booking_date->format('d M Y') }}</li>
                                                <li>Waktu: {{ $booking->start_time->format('H:i') }} - {{ $booking->end_time->format('H:i') }}</li>
                                            </ul>
                                        </div>
                                        <div class="mb-3">
                                            <label for="admin_notes{{ $booking->id }}" class="form-label">
                                                Alasan Penolakan <span class="text-danger">*</span>
                                            </label>
                                            <textarea name="admin_notes" 
                                                      id="admin_notes{{ $booking->id }}" 
                                                      class="form-control" 
                                                      rows="3" 
                                                      required 
                                                      placeholder="Jelaskan alasan penolakan..."></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-danger">Tolak Booking</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif
            @empty
                <div class="text-center py-5">
                    <i class="bi bi-calendar-x fs-1 text-muted"></i>
                    <h5 class="text-muted mt-3">Tidak Ada Booking</h5>
                    <p class="text-muted">Tidak ada booking yang sesuai dengan filter yang dipilih.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Pagination -->
    @if($bookings->hasPages())
        <div class="row mt-4">
            <div class="col-12 d-flex justify-content-center">
                {{ $bookings->appends(request()->query())->links() }}
            </div>
        </div>
    @endif
</div>
@endsection