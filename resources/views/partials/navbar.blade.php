<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <div class="container">
    <a class="navbar-brand" href="#">Booking Ruangan</a>
    <div class="collapse navbar-collapse">
      <ul class="navbar-nav ms-auto">
        @guest
          <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Login</a></li>
          <li class="nav-item"><a class="nav-link" href="{{ route('register') }}">Register</a></li>
        @else
          @if(Auth::user()->role == 'admin')
            <li class="nav-item"><a class="nav-link" href="/admin/bookings">Admin Panel</a></li>
          @else
            <li class="nav-item"><a class="nav-link" href="/booking">My Booking</a></li>
          @endif
          <li class="nav-item">
            <form action="{{ route('logout') }}" method="POST">@csrf
              <button class="btn btn-link nav-link">Logout</button>
            </form>
          </li>
        @endguest
      </ul>
    </div>
  </div>
</nav>
