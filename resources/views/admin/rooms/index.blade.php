@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-1">Kelola Ruangan</h1>
                    <p class="text-muted">Daftar semua ruangan yang tersedia</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>
                        Dashboard
                    </a>
                    <a href="{{ route('admin.rooms.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>
                        Tambah Ruangan
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        @forelse($rooms as $room)
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card room-card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <h5 class="card-title mb-0">{{ $room->name }}</h5>
                            <div class="d-flex gap-1">
                                <span class="badge {{ $room->is_active ? 'bg-success' : 'bg-secondary' }}">
                                    <i class="bi bi-{{ $room->is_active ? 'check-circle' : 'x-circle' }} me-1"></i>
                                    {{ $room->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-people text-primary me-2"></i>
                                <span class="text-muted">Kapasitas: {{ $room->capacity }} orang</span>
                            </div>
                            @if($room->location)
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bi bi-geo-alt text-primary me-2"></i>
                                    <span class="text-muted">{{ $room->location }}</span>
                                </div>
                            @endif
                            <div class="d-flex align-items-center">
                                <i class="bi bi-calendar-check text-primary me-2"></i>
                                <span class="text-muted">{{ $room->bookings()->count() }} booking total</span>
                            </div>
                        </div>

                        @if($room->description)
                            <p class="card-text text-muted small mb-3">
                                {{ Str::limit($room->description, 100) }}
                            </p>
                        @endif

                        @if($room->facilities && count($room->facilities) > 0)
                            <div class="mb-3">
                                <small class="text-muted d-block mb-2">Fasilitas:</small>
                                <div class="d-flex flex-wrap gap-1">
                                    @foreach(array_slice($room->facilities, 0, 3) as $facility)
                                        <span class="badge bg-light text-dark">
                                            <i class="bi bi-check me-1"></i>
                                            {{ $facility }}
                                        </span>
                                    @endforeach
                                    @if(count($room->facilities) > 3)
                                        <span class="badge bg-light text-dark">
                                            +{{ count($room->facilities) - 3 }} lainnya
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                    
                    <div class="card-footer bg-white border-0 pt-0">
                        <div class="row g-2">
                            <div class="col">
                                <a href="{{ route('rooms.show', $room) }}" 
                                   class="btn btn-outline-primary btn-sm w-100">
                                    <i class="bi bi-eye me-1"></i>
                                    Detail
                                </a>
                            </div>
                            <div class="col">
                                <a href="{{ route('admin.rooms.edit', $room) }}" 
                                   class="btn btn-outline-warning btn-sm w-100">
                                    <i class="bi bi-pencil me-1"></i>
                                    Edit
                                </a>
                            </div>
                            <div class="col">
                                <form action="{{ route('admin.rooms.toggle', $room) }}" method="POST" class="d-inline w-100">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" 
                                            class="btn btn-{{ $room->is_active ? 'outline-danger' : 'outline-success' }} btn-sm w-100"
                                            onclick="return confirm('Yakin ingin mengubah status ruangan ini?')">
                                        <i class="bi bi-{{ $room->is_active ? 'x-circle' : 'check-circle' }} me-1"></i>
                                        {{ $room->is_active ? 'Nonaktif' : 'Aktifkan' }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-door-closed fs-1 text-muted mb-3"></i>
                        <h5 class="text-muted">Belum Ada Ruangan</h5>
                        <p class="text-muted mb-4">Belum ada ruangan yang terdaftar dalam sistem.</p>
                        <a href="{{ route('admin.rooms.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-2"></i>
                            Tambah Ruangan Pertama
                        </a>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($rooms->hasPages())
        <div class="row mt-4">
            <div class="col-12 d-flex justify-content-center">
                {{ $rooms->links() }}
            </div>
        </div>
    @endif
</div>
@endsection-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-clock-history me-2"></i>
                        Booking Menunggu Persetujuan
                    </h5>
                    <span class="badge bg-warning">{{ $pendingBookings->count() }}</span>
                </div>
                <div class="card-body">
                    @forelse($pendingBookings as $booking)
                        <div class="border-bottom pb-3 mb-3 last:border-0 last:pb-0 last:mb-0">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <h6 class="mb-1">{{ $booking->room->name }}</h6>
                                    <p class="text-muted mb-1">
                                        <i class="bi bi-person me-1"></i>
                                        {{ $booking->user->name }}
                                    </p>
                                </div>
                                <span class="badge bg-warning">Pending</span>
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <small class="text-muted">
                                        <i class="bi bi-calendar me-1"></i>
                                        {{ $booking->booking_date->format('d M Y') }}
                                    </small>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted">
                                        <i class="bi bi-clock me-1"></i>
                                        {{ $booking->start_time->format('H:i') }} - {{ $booking->end_time->format('H:i') }}
                                    </small>
                                </div>
                            </div>
                            <p class="mb-2 small">{{ Str::limit($booking->purpose, 80) }}</p>
                            <div class="d-flex gap-2">
                                <form action="{{ route('admin.bookings.approve', $booking) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-success btn-sm">
                                        <i class="bi bi-check me-1"></i>
                                        Setujui
                                    </button>
                                </form>
                                <button type="button" 
                                        class="btn btn-danger btn-sm" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#rejectModal{{ $booking->id }}">
                                    <i class="bi bi-x me-1"></i>
                                    Tolak
                                </button>
                            </div>
                        </div>

                        <!-- Reject Modal -->
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
                                            <p>Anda akan menolak booking ruangan <strong>{{ $booking->room->name }}</strong> oleh <strong>{{ $booking->user->name }}</strong>.</p>
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
                    @empty
                        <div class="text-center py-3">
                            <i class="bi bi-check-circle fs-1 text-success"></i>
                            <p class="text-muted mt-2 mb-0">Tidak ada booking yang menunggu persetujuan</p>
                        </div>
                    @endforelse

                    @if($pendingBookings->count() > 0)
                        <div class="text-center mt-3">
                            <a href="{{ route('admin.bookings', ['status' => 'pending']) }}" class="btn btn-outline-primary btn-sm">
                                Lihat Semua Pending
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Recent Bookings -->
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-clock-history me-2"></i>
                        Booking Terbaru
                    </h5>
                </div>
                <div class="card-body">
                    @forelse($recentBookings as $booking)
                        <div class="border-bottom pb-3 mb-3 last:border-0 last:pb-0 last:mb-0">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <h6 class="mb-1">{{ $booking->room->name }}</h6>
                                    <p class="text-muted mb-1">
                                        <i class="bi bi-person me-1"></i>
                                        {{ $booking->user->name }}
                                    </p>
                                </div>
                                <span class="badge bg-{{ $booking->status_color }}">{{ ucfirst($booking->status) }}</span>
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <small class="text-muted">
                                        <i class="bi bi-calendar me-1"></i>
                                        {{ $booking->booking_date->format('d M Y') }}
                                    </small>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted">
                                        <i class="bi bi-clock me-1"></i>
                                        {{ $booking->start_time->format('H:i') }} - {{ $booking->end_time->format('H:i') }}
                                    </small>
                                </div>
                            </div>
                            <p class="mb-2 small">{{ Str::limit($booking->purpose, 80) }}</p>
                        </div>
                    @empty
                        <div class="text-center py-3">
                            <i class="bi bi-calendar-x fs-1 text-muted"></i>
                            <p class="text-muted mt-2 mb-0">Belum ada booking terbaru</p>
                        </div>
                    @endforelse

                    @if($recentBookings->count() > 0)
                        <div class="text-center mt-3">
                            <a href="{{ route('admin.bookings') }}" class="btn btn-outline-primary btn-sm">
                                Lihat Semua Booking
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection