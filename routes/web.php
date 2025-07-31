<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\Admin\AdminController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Manager\ManagerController;

// Public Routes
Route::get('/', function () {
    return view('welcome');
});

// Authentication Routes (Laravel Breeze)
require __DIR__.'/auth.php';

// Admin Auth Routes
Route::prefix('admin')->name('admin.')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    });

    // Admin Dashboard - after login
    Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
    });
});

// Fallback
Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});

// Protected Routes (Require Authentication)
Route::middleware('auth')->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Profile Management
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Room Routes
    Route::prefix('rooms')->name('rooms.')->group(function () {
        Route::get('/', [RoomController::class, 'index'])->name('index');
        Route::get('/{room}', [RoomController::class, 'show'])->name('show');
        Route::get('/{room}/calendar', [RoomController::class, 'calendar'])->name('calendar');
        Route::get('/search/available', [RoomController::class, 'searchAvailable'])->name('search.available');
    });
    
    // Booking Routes
    Route::prefix('bookings')->name('bookings.')->group(function () {
        Route::get('/', [BookingController::class, 'index'])->name('index');
        Route::get('/create', [BookingController::class, 'create'])->name('create');
        Route::post('/', [BookingController::class, 'store'])->name('store');
        Route::get('/{booking}', [BookingController::class, 'show'])->name('show');
        Route::get('/{booking}/edit', [BookingController::class, 'edit'])->name('edit');
        Route::put('/{booking}', [BookingController::class, 'update'])->name('update');
        Route::delete('/{booking}', [BookingController::class, 'destroy'])->name('destroy');
        Route::post('/{booking}/cancel', [BookingController::class, 'cancel'])->name('cancel');
        Route::post('/booking', [BookingController::class, 'store'])->name('booking.store');
        
        // Booking Status Management
        Route::post('/{booking}/confirm', [BookingController::class, 'confirm'])->name('confirm');
        Route::post('/{booking}/reject', [BookingController::class, 'reject'])->name('reject');
        
        // Calendar and API Routes
        Route::get('/calendar/events', [BookingController::class, 'calendarEvents'])->name('calendar.events');
        Route::get('/room/{room}/availability', [BookingController::class, 'roomAvailability'])->name('room.availability');
    });
    
    // My Bookings (User's own bookings)
    Route::prefix('my-bookings')->name('my-bookings.')->group(function () {
        Route::get('/', [BookingController::class, 'myBookings'])->name('index');
        Route::get('/history', [BookingController::class, 'myBookingHistory'])->name('history');
    });
    
    // Reports (Accessible by Manager and Admin)
    Route::middleware(['role:admin,manager'])->prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/bookings', [ReportController::class, 'bookings'])->name('bookings');
        Route::get('/rooms', [ReportController::class, 'rooms'])->name('rooms');
        Route::get('/users', [ReportController::class, 'users'])->name('users');
        Route::get('/export/bookings', [ReportController::class, 'exportBookings'])->name('export.bookings');
        Route::get('/export/rooms', [ReportController::class, 'exportRooms'])->name('export.rooms');
    });
});

// Manager Routes
Route::prefix('manager')->name('manager.')->middleware(['auth', 'manager'])->group(function() {
Route::middleware(['auth', 'role:manager,admin'])->prefix('manager')->name('manager.')->group(function () {
    Route::get('/dashboard', [ManagerController::class, 'dashboard'])->name('dashboard');
});

    // Booking Management
    Route::prefix('bookings')->name('bookings.')->group(function () {
        Route::get('/', [ManagerController::class, 'bookings'])->name('index');
        Route::get('/pending', [ManagerController::class, 'pendingBookings'])->name('pending');
        Route::get('/confirmed', [ManagerController::class, 'confirmedBookings'])->name('confirmed');
        Route::get('/cancelled', [ManagerController::class, 'cancelledBookings'])->name('cancelled');
        Route::post('/{booking}/approve', [ManagerController::class, 'approveBooking'])->name('approve');
        Route::post('/{booking}/reject', [ManagerController::class, 'rejectBooking'])->name('reject');
    });
    
    // Room Management (Manager can manage assigned rooms)
    Route::prefix('rooms')->name('rooms.')->group(function () {
        Route::get('/', [ManagerController::class, 'rooms'])->name('index');
        Route::get('/{room}/schedule', [ManagerController::class, 'roomSchedule'])->name('schedule');
        Route::post('/{room}/block', [ManagerController::class, 'blockRoom'])->name('block');
        Route::delete('/schedule/{schedule}', [ManagerController::class, 'removeBlock'])->name('remove-block');
    });
});

// Admin Routes (hanya bisa diakses oleh super admin)
Route::middleware(['auth', 'superadmin'])->prefix('admin')->group(function () {
    // Register Admin (hanya untuk super admin)
    Route::get('/register', [AdminController::class, 'showRegistrationForm'])->name('admin.register');
    Route::post('/register', [AdminController::class, 'register'])->name('admin.register.submit');

    // Admin List
    Route::get('/admins', [AdminController::class, 'index'])->name('admin.list');
}); 

// Admin auth routes dengan namespace yang benar
Route::prefix('admin')->name('admin.')->group(function () {
    Route::prefix('auth')->group(function () {
        // GET routes
        Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
        Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
        Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('forgot-password');
        
        // POST routes
        Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
        Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
        Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('forgot-password.submit');
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
        
        // Password reset routes
        Route::get('/reset-password/{token}', [AuthController::class, 'showResetPasswordForm'])->name('reset-password');
        Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('reset-password.submit');
    });
});

    Route::fallback(function () {
    return response()->view('errors.404', [], 404);
    });


    // User Management
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::get('/{user}', [UserController::class, 'show'])->name('show');
        Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
        Route::put('/{user}', [UserController::class, 'update'])->name('update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
        Route::post('/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('toggle-status');
    });
    
    // Room Management (Full CRUD)
    Route::prefix('rooms')->name('rooms.')->group(function () {
        Route::get('/', [AdminController::class, 'rooms'])->name('index');
        Route::get('/create', [AdminController::class, 'createRoom'])->name('create');
        Route::post('/', [AdminController::class, 'storeRoom'])->name('store');
        Route::get('/{room}/edit', [AdminController::class, 'editRoom'])->name('edit');
        Route::put('/{room}', [AdminController::class, 'updateRoom'])->name('update');
        Route::delete('/{room}', [AdminController::class, 'destroyRoom'])->name('destroy');
        Route::post('/{room}/toggle-status', [AdminController::class, 'toggleRoomStatus'])->name('toggle-status');
        
        // Room Manager Assignment
        Route::get('/{room}/managers', [AdminController::class, 'roomManagers'])->name('managers');
        Route::post('/{room}/managers', [AdminController::class, 'assignManager'])->name('assign-manager');
        Route::delete('/{room}/managers/{user}', [AdminController::class, 'removeManager'])->name('remove-manager');
    });
    
    // Booking Management (All bookings)
    Route::prefix('bookings')->name('bookings.')->group(function () {
        Route::get('/', [AdminController::class, 'bookings'])->name('index');
        Route::get('/{booking}', [AdminController::class, 'showBooking'])->name('show');
        Route::post('/{booking}/force-confirm', [AdminController::class, 'forceConfirm'])->name('force-confirm');
        Route::post('/{booking}/force-cancel', [AdminController::class, 'forceCancel'])->name('force-cancel');
    });
    
    // System Settings
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [AdminController::class, 'settings'])->name('index');
        Route::post('/update', [AdminController::class, 'updateSettings'])->name('update');
        Route::get('/backup', [AdminController::class, 'backup'])->name('backup');
        Route::get('/logs', [AdminController::class, 'logs'])->name('logs');
    });
    
    // Statistics and Analytics
    Route::prefix('analytics')->name('analytics.')->group(function () {
        Route::get('/', [AdminController::class, 'analytics'])->name('index');
        Route::get('/bookings-chart', [AdminController::class, 'bookingsChart'])->name('bookings-chart');
        Route::get('/rooms-usage', [AdminController::class, 'roomsUsage'])->name('rooms-usage');
        Route::get('/users-activity', [AdminController::class, 'usersActivity'])->name('users-activity');
    });
// });

// API Routes (untuk AJAX calls, mobile app, etc.)
Route::middleware('auth')->prefix('api')->name('api.')->group(function () {
    
    // Room API
    Route::get('/rooms', [RoomController::class, 'apiIndex'])->name('rooms.index');
    Route::get('/rooms/{room}', [RoomController::class, 'apiShow'])->name('rooms.show');
    Route::get('/rooms/{room}/availability/{date}', [RoomController::class, 'apiAvailability'])->name('rooms.availability');
    
    // Booking API
    Route::get('/bookings', [BookingController::class, 'apiIndex'])->name('bookings.index');
    Route::post('/bookings', [BookingController::class, 'apiStore'])->name('bookings.store');
    Route::get('/bookings/{booking}', [BookingController::class, 'apiShow'])->name('bookings.show');
    Route::put('/bookings/{booking}', [BookingController::class, 'apiUpdate'])->name('bookings.update');
    Route::delete('/bookings/{booking}', [BookingController::class, 'apiDestroy'])->name('bookings.destroy');
    
    // Calendar API
    Route::get('/calendar/events', [BookingController::class, 'apiCalendarEvents'])->name('calendar.events');
    Route::get('/calendar/room/{room}/events', [BookingController::class, 'apiRoomEvents'])->name('calendar.room-events');
    
    // Quick Actions API
    Route::post('/bookings/{booking}/quick-confirm', [BookingController::class, 'apiQuickConfirm'])->name('bookings.quick-confirm');
    Route::post('/bookings/{booking}/quick-cancel', [BookingController::class, 'apiQuickCancel'])->name('bookings.quick-cancel');
    
    // Search API
    Route::get('/search/rooms', [RoomController::class, 'apiSearch'])->name('search.rooms');
    Route::get('/search/bookings', [BookingController::class, 'apiSearch'])->name('search.bookings');
    Route::get('/search/users', [UserController::class, 'apiSearch'])->name('search.users');
});

// Webhook Routes (for external integrations)
Route::prefix('webhooks')->name('webhooks.')->group(function () {
    Route::post('/booking-reminder', [BookingController::class, 'bookingReminder'])->name('booking-reminder');
    Route::post('/room-maintenance', [RoomController::class, 'maintenanceNotification'])->name('room-maintenance');
});

// Public API (without authentication - for display boards, etc.)
Route::prefix('public-api')->name('public-api.')->group(function () {
    Route::get('/rooms/{room}/current-booking', [RoomController::class, 'currentBooking'])->name('rooms.current-booking');
    Route::get('/rooms/{room}/today-schedule', [RoomController::class, 'todaySchedule'])->name('rooms.today-schedule');
    Route::get('/display/rooms', [RoomController::class, 'displayRooms'])->name('display.rooms');
});

// Fallback Route
Route::fallback(function () {
    return view('errors.404');
});