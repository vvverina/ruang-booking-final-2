<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'capacity',
        'location',
        'floor',
        'facilities',
        'price_per_hour',
        'status',
        'image',
        'requires_approval'
    ];

    protected $casts = [
        'facilities' => 'array',
        'price_per_hour' => 'decimal:2',
        'requires_approval' => 'boolean'
    ];

    // Relationships
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function managers()
    {
        return $this->belongsToMany(User::class, 'room_managers');
    }

    public function schedules()
    {
        return $this->hasMany(RoomSchedule::class);
    }

    // Scopes
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    public function scopeByCapacity($query, $minCapacity)
    {
        return $query->where('capacity', '>=', $minCapacity);
    }

    // Helper methods
    public function isAvailable()
    {
        return $this->status === 'available';
    }

    public function isAvailableAt($date, $startTime, $endTime, $excludeBookingId = null)
    {
        $query = $this->bookings()
            ->where('booking_date', $date)
            ->where('status', '!=', 'cancelled')
            ->where(function ($q) use ($startTime, $endTime) {
                $q->whereBetween('start_time', [$startTime, $endTime])
                  ->orWhereBetween('end_time', [$startTime, $endTime])
                  ->orWhere(function ($q2) use ($startTime, $endTime) {
                      $q2->where('start_time', '<=', $startTime)
                         ->where('end_time', '>=', $endTime);
                  });
            });

        if ($excludeBookingId) {
            $query->where('id', '!=', $excludeBookingId);
        }

        return $query->doesntExist();
    }

    public function getTodayBookings()
    {
        return $this->bookings()
            ->where('booking_date', now()->toDateString())
            ->where('status', '!=', 'cancelled')
            ->orderBy('start_time')
            ->get();
    }

    public function getImageUrlAttribute()
    {
        return $this->image ? asset('storage/' . $this->image) : asset('images/default-room.jpg');
    }
}
