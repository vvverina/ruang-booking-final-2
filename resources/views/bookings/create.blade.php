@extends('layouts.app')

@section('content')
<div class="card">
  <div class="card-header">Booking Ruangan</div>
  <div class="card-body">
    <form method="POST" action="/booking">
      @csrf
      <div class="mb-3">
        <label>Ruangan</label>
        <select name="room_id" class="form-control" id="room_id">
          @foreach ($rooms as $room)
            <option value="{{ $room->id }}">{{ $room->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="mb-3">
        <label>Tanggal</label>
        <input type="date" name="booking_date" id="booking_date" class="form-control">
      </div>
      <div id="availabilityMsg" class="mb-2 text-info"></div>
      <button type="submit" class="btn btn-primary">Submit Booking</button>
    </form>
  </div>
</div>

<script>
  document.getElementById('booking_date').addEventListener('change', checkAvailability);
  document.getElementById('room_id').addEventListener('change', checkAvailability);

  function checkAvailability() {
    let date = document.getElementById('booking_date').value;
    let room = document.getElementById('room_id').value;
    if (date && room) {
      fetch(`/booking/check?booking_date=${date}&room_id=${room}`)
        .then(res => res.json())
        .then(data => {
          const msg = document.getElementById('availabilityMsg');
          msg.innerText = data.available ? 'Tersedia!' : 'Sudah dibooking!';
          msg.className = data.available ? 'text-success' : 'text-danger';
        });
    }
  }
</script>
@endsection
