<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Outlet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AdminUserController extends Controller
{
    public function index()
    {
        // Only show users from current user's outlet
        $users = User::where('outlet_id', auth()->user()->outlet_id)
            ->orderBy('name')
            ->paginate(20);
            
        return view('admin.users.index', compact('users'));
    }
    
    public function create()
    {
        // Only allow owner to create users
        if (auth()->user()->role !== 'owner') {
            return redirect()->route('admin.users.index')
                ->with('error', 'Only owners can create new users.');
        }
        
        return view('admin.users.create');
    }
    
    public function store(Request $request)
    {
        // Only allow owner to create users
        if (auth()->user()->role !== 'owner') {
            return redirect()->route('admin.users.index')
                ->with('error', 'Only owners can create new users.');
        }
        
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', Password::defaults()],
            'role' => ['required', 'in:owner,cashier,supervisor'],
            'phone' => ['nullable', 'string', 'max:20'],
        ]);
        
        // Always set outlet_id to current user's outlet
        $validated['outlet_id'] = auth()->user()->outlet_id;
        
        // Hash the password
        $validated['password'] = Hash::make($validated['password']);
        
        User::create($validated);
        
        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully.');
    }
    
    public function edit(User $user)
    {
        // Only allow editing users in same outlet
        if ($user->outlet_id !== auth()->user()->outlet_id) {
            abort(403, 'Unauthorized access to user.');
        }
        
        // Only owner can edit other owners
        if ($user->role === 'owner' && auth()->user()->role !== 'owner') {
            return redirect()->route('admin.users.index')
                ->with('error', 'Only owners can edit owner accounts.');
        }
        
        return view('admin.users.edit', compact('user'));
    }
    
    public function update(Request $request, User $user)
    {
        // Only allow editing users in same outlet
        if ($user->outlet_id !== auth()->user()->outlet_id) {
            abort(403, 'Unauthorized access to user.');
        }
        
        // Only owner can edit other owners
        if ($user->role === 'owner' && auth()->user()->role !== 'owner') {
            return redirect()->route('admin.users.index')
                ->with('error', 'Only owners can edit owner accounts.');
        }
        
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'role' => ['required', 'in:owner,cashier,supervisor'],
            'phone' => ['nullable', 'string', 'max:20'],
            'is_active' => ['boolean'],
        ]);
        
        // Only owner can change roles
        if (auth()->user()->role !== 'owner') {
            unset($validated['role']);
        }
        
        $validated['is_active'] = $request->has('is_active');
        
        $user->update($validated);
        
        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully.');
    }
    
    public function changePassword(Request $request, User $user)
    {
        // Only allow editing users in same outlet
        if ($user->outlet_id !== auth()->user()->outlet_id) {
            abort(403, 'Unauthorized access to user.');
        }
        
        // Only owner can change other owner passwords
        if ($user->role === 'owner' && auth()->user()->role !== 'owner') {
            return redirect()->route('admin.users.index')
                ->with('error', 'Only owners can change owner passwords.');
        }
        
        // Current user can only change their own password with current password validation
        if ($user->id === auth()->id()) {
            $validated = $request->validate([
                'current_password' => ['required', 'current_password'],
                'password' => ['required', 'confirmed', Password::defaults()],
            ]);
        } else {
            // Owners can change other users' passwords without current password
            $validated = $request->validate([
                'password' => ['required', 'confirmed', Password::defaults()],
            ]);
        }
        
        $user->update([
            'password' => Hash::make($validated['password']),
        ]);
        
        return redirect()->back()
            ->with('success', 'Password changed successfully.');
    }
    
    public function profile()
    {
        $user = auth()->user();
        return view('admin.users.profile', compact('user'));
    }
    
    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone' => ['nullable', 'string', 'max:20'],
        ]);
        
        $user->update($validated);
        
        return redirect()->route('admin.profile')
            ->with('success', 'Profile updated successfully.');
    }
}