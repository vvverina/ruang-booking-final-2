<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id',
        'title',
        'description',
        'date',
        'start_time',
        'end_time',
        'type',
        'recurring',
        'recurring_type',
        'recurring_end_date'
    ];

    protected $casts = [
        'date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'recurring' => 'boolean',
        'recurring_end_date' => 'date'
    ];

    // Relationships
    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('date', '>=', now()->toDateString());
    }

    public function scopeMaintenance($query)
    {
        return $query->where('type', 'maintenance');
    }
}