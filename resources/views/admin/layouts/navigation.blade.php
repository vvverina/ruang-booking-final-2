@extends('admin.layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-3">
            <div class="list-group mb-4">
                <a href="{{ route('admin.dashboard') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt me-2"></i>Dashboard Admin
                </a>
                <a href="{{ route('admin.rooms.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.rooms.*') ? 'active' : '' }}">
                    <i class="fas fa-door-open me-2"></i>Kelola Ruangan
                </a>
                <a href="{{ route('admin.bookings') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.bookings') ? 'active' : '' }}">
                    <i class="fas fa-calendar-check me-2"></i>Kelola Booking
                </a>
                <a href="{{ route('reports.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                    <i class="fas fa-chart-line me-2"></i>Reports
                </a>
                <a href="{{ route('profile.edit') }}" class="list-group-item list-group-item-action {{ request()->routeIs('profile.edit') ? 'active' : '' }}">
                    <i class="fas fa-user me-2"></i>Profil Admin
                </a>
            </div>
        </div>

        <div class="col-md-9">
            @yield('admin-content')
        </div>
    </div>
</div>
@endsection
