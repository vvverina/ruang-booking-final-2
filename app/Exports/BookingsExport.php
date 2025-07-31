<?php

namespace App\Exports;

use App\Models\Booking;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class BookingsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $filters;
    
    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }
    
    public function collection()
    {
        $query = Booking::with(['user', 'room']);
        
        if (isset($this->filters['date_from'])) {
            $query->where('booking_date', '>=', $this->filters['date_from']);
        }
        
        if (isset($this->filters['date_to'])) {
            $query->where('booking_date', '<=', $this->filters['date_to']);
        }
        
        if (isset($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }
        
        if (isset($this->filters['room_id'])) {
            $query->where('room_id', $this->filters['room_id']);
        }
        
        return $query->latest()->get();
    }
    
    public function headings(): array
    {
        return [
            'ID',
            'Title',
            'User',
            'Room',
            'Date',
            'Start Time',
            'End Time',
            'Participants',
            'Status',
            'Created At',
            'Confirmed At',
            'Confirmed By'
        ];
    }
    
    public function map($booking): array
    {
        return [
            $booking->id,
            $booking->title,
            $booking->user->name,
            $booking->room->name,
            $booking->booking_date,
            $booking->start_time,
            $booking->end_time,
            $booking->participant_count,
            ucfirst($booking->status),
            $booking->created_at->format('Y-m-d H:i:s'),
            $booking->confirmed_at ? $booking->confirmed_at->format('Y-m-d H:i:s') : '',
            $booking->confirmedBy ? $booking->confirmedBy->name : ''
        ];
    }
}