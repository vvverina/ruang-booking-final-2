<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('employee_id', 'LIKE', "%{$search}%");
            });
        }
        
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }
        
        if ($request->filled('department')) {
            $query->where('department', $request->department);
        }
        
        $users = $query->withCount('bookings')->latest()->paginate(15);
        
        $departments = User::distinct()->whereNotNull('department')->pluck('department');
        
        return view('admin.users.index', compact('users', 'departments'));
    }
    
    public function create()
    {
        $departments = User::distinct()->whereNotNull('department')->pluck('department');
        return view('admin.users.create', compact('departments'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'department' => 'nullable|string|max:100',
            'role' => 'required|in:admin,manager,user',
            'employee_id' => 'nullable|string|max:50|unique:users',
        ]);
        
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'department' => $request->department,
            'role' => $request->role,
            'employee_id' => $request->employee_id,
            'email_verified_at' => now(),
        ]);
        
        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil dibuat!');
    }
    
    public function show(User $user)
    {
        $user->load(['bookings.room', 'rooms']);
        
        $bookingStats = [
            'total' => $user->bookings()->count(),
            'pending' => $user->bookings()->where('status', 'pending')->count(),
            'confirmed' => $user->bookings()->where('status', 'confirmed')->count(),
            'cancelled' => $user->bookings()->where('status', 'cancelled')->count(),
            'this_month' => $user->bookings()->whereMonth('booking_date', now()->month)->count(),
        ];
        
        $recentBookings = $user->bookings()
            ->with('room')
            ->latest()
            ->take(10)
            ->get();
        
        return view('admin.users.show', compact('user', 'bookingStats', 'recentBookings'));
    }
    
    public function edit(User $user)
    {
        $departments = User::distinct()->whereNotNull('department')->pluck('department');
        return view('admin.users.edit', compact('user', 'departments'));
    }
    
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'department' => 'nullable|string|max:100',
            'role' => 'required|in:admin,manager,user',
            'employee_id' => ['nullable', 'string', 'max:50', Rule::unique('users')->ignore($user->id)],
        ]);
        
        $data = $request->only(['name', 'email', 'phone', 'department', 'role', 'employee_id']);
        
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }
        
        $user->update($data);
        
        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil diperbarui!');
    }
    
    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak dapat menghapus akun sendiri.');
        }
        
        if ($user->bookings()->whereIn('status', ['pending', 'confirmed'])->exists()) {
            return back()->with('error', 'Tidak dapat menghapus user yang memiliki booking aktif.');
        }
        
        $user->delete();
        
        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil dihapus!');
    }
    
    public function toggleStatus(User $user)
    {
        // Implementasi toggle status jika diperlukan
        return back()->with('success', 'Status user berhasil diubah!');
    }
    
    public function apiSearch(Request $request)
    {
        $query = User::query();
        
        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('employee_id', 'LIKE', "%{$search}%");
            });
        }
        
        return $query->limit(10)->get(['id', 'name', 'email', 'role']);
    }
}