<?php

namespace App\Exports;

use App\Models\Room;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class RoomsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $filters;
    
    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }
    
    public function collection()
    {
        $query = Room::withCount('bookings');
        
        if (isset($this->filters['location'])) {
            $query->where('location', 'LIKE', '%' . $this->filters['location'] . '%');
        }
        
        if (isset($this->filters['capacity_min'])) {
            $query->where('capacity', '>=', $this->filters['capacity_min']);
        }
        
        if (isset($this->filters['capacity_max'])) {
            $query->where('capacity', '<=', $this->filters['capacity_max']);
        }
        
        return $query->get();
    }
    
    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Code',
            'Location',
            'Floor',
            'Capacity',
            'Price per Hour',
            'Status',
            'Total Bookings',
            'Facilities',
            'Created At'
        ];
    }
    
    public function map($room): array
    {
        return [
            $room->id,
            $room->name,
            $room->code,
            $room->location,
            $room->floor,
            $room->capacity,
            $room->price_per_hour,
            ucfirst($room->status),
            $room->bookings_count,
            is_array($room->facilities) ? implode(', ', $room->facilities) : '',
            $room->created_at->format('Y-m-d H:i:s')
        ];
    }
}