<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <nav class="bg-white shadow p-4 mb-4">
        <div class="container mx-auto flex justify-between">
            <div class="text-lg font-bold">User Panel</div>
            <div>
                <a href="/user/dashboard" class="text-blue-500 hover:underline mr-4">Dashboard</a>
                <a href="/user/bookings" class="text-blue-500 hover:underline">Booking</a>
            </div>
        </div>
    </nav>
    <main class="container mx-auto">
        @yield('content')
    </main>
</body>
</html>
