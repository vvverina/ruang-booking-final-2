<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'room_id',
        'booking_code',
        'title',
        'description',
        'booking_date',
        'start_time',
        'end_time',
        'participant_count',
        'status',
        'priority',
        'total_cost',
        'notes',
        'cancellation_reason',
        'confirmed_at',
        'cancelled_at',
        'confirmed_by'
    ];

    protected $casts = [
        'booking_date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'total_cost' => 'decimal:2',
        'confirmed_at' => 'datetime',
        'cancelled_at' => 'datetime'
    ];

    protected $dates = ['booking_date', 'start_time', 'end_time'];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function confirmedBy()
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    public function histories()
    {
        return $this->hasMany(BookingHistory::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    public function scopeToday($query)
    {
        return $query->where('booking_date', now()->toDateString());
    }

    public function scopeUpcoming($query)
    {
        return $query->where('booking_date', '>=', now()->toDateString());
    }

    // Helper methods
    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isConfirmed()
    {
        return $this->status === 'confirmed';
    }

    public function isCancelled()
    {
        return $this->status === 'cancelled';
    }

    public function canBeCancelled()
    {
        if ($this->isCancelled()) {
            return false;
        }

        $bookingDateTime = Carbon::parse($this->booking_date . ' ' . $this->start_time);
        return $bookingDateTime->isFuture();
    }

    public function getDurationInHours()
    {
        $start = Carbon::parse($this->start_time);
        $end = Carbon::parse($this->end_time);
        return $end->diffInHours($start);
    }

    public function calculateTotalCost()
    {
        $duration = $this->getDurationInHours();
        return $duration * $this->room->price_per_hour;
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => '<span class="badge badge-warning">Menunggu</span>',
            'confirmed' => '<span class="badge badge-success">Dikonfirmasi</span>',
            'cancelled' => '<span class="badge badge-danger">Dibatalkan</span>',
            'completed' => '<span class="badge badge-info">Selesai</span>'
        ];

        return $badges[$this->status] ?? $this->status;
    }

    // Boot method untuk auto-generate booking code
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($booking) {
            $booking->booking_code = self::generateBookingCode();
            $booking->total_cost = $booking->calculateTotalCost();
        });
    }

    public static function generateBookingCode()
    {
        $prefix = 'BK';
        $date = now()->format('ymd');
        $count = self::whereDate('created_at', now()->toDateString())->count() + 1;
        
        return $prefix . $date . str_pad($count, 3, '0', STR_PAD_LEFT);
    }
}
