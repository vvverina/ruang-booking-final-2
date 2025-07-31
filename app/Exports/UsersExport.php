<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class UsersExport implements FromCollection, WithHeadings, WithMapping
{
    protected $filters;
    
    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }
    
    public function collection()
    {
        $query = User::withCount('bookings');
        
        if (isset($this->filters['role'])) {
            $query->where('role', $this->filters['role']);
        }
        
        if (isset($this->filters['department'])) {
            $query->where('department', $this->filters['department']);
        }
        
        return $query->get();
    }
    
    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Email',
            'Employee ID',
            'Phone',
            'Department',
            'Role',
            'Total Bookings',
            'Email Verified',
            'Created At'
        ];
    }
    
    public function map($user): array
    {
        return [
            $user->id,
            $user->name,
            $user->email,
            $user->employee_id,
            $user->phone,
            $user->department,
            ucfirst($user->role),
            $user->bookings_count,
            $user->email_verified_at ? 'Yes' : 'No',
            $user->created_at->format('Y-m-d H:i:s')
        ];
    }
}