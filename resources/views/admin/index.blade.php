@extends('layouts.app')

@section('content')
<div class="card">
  <div class="card-header">Semua Booking Ruangan</div>
  <div class="card-body">
    <table class="table">
      <thead>
        <tr>
          <th>Nama</th>
          <th>Ruangan</th>
          <th>Tanggal</th>
          <th>Status</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($bookings as $b)
          <tr>
            <td>{{ $b->user->name }}</td>
            <td>{{ $b->room->name }}</td>
            <td>{{ $b->booking_date }}</td>
            <td>{{ ucfirst($b->status) }}</td>
            <td>
              @if($b->status == 'pending')
                <form method="POST" action="/admin/bookings/{{ $b->id }}/approve" style="display:inline">@csrf<button class="btn btn-sm btn-success">Setujui</button></form>
                <form method="POST" action="/admin/bookings/{{ $b->id }}/reject" style="display:inline">@csrf<button class="btn btn-sm btn-warning">Tolak</button></form>
              @endif
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endsection
