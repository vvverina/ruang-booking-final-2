<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',           // Nomor telepon untuk konfirmasi booking
        'department',      // Departemen/unit kerja
        'role',           // Admin, user, manager, etc
        'employee_id',    // ID karyawan jika diperlukan
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relationships untuk booking system
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function rooms()
    {
        return $this->belongsToMany(Room::class, 'room_managers');
    }

    // Helper methods
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isManager()
    {
        return $this->role === 'manager';
    }

    public function canBookRoom($room)
    {
        // Logic untuk mengecek apakah user bisa booking ruangan tertentu
        return true; // Customize sesuai business logic
    }

    public function getActiveBookings()
    {
        return $this->bookings()
            ->where('status', 'confirmed')
            ->where('start_time', '>=', now())
            ->get();
    }
}