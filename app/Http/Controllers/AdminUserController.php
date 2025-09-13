<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Outlet;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
use Hash;
use Storage;
use App\Models\UserActivity;

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
    
    public function changePassword(Request $request, User $user = null)
    {
        // If no user is provided, use the authenticated user (for profile change)
        if (!$user) {
            $user = auth()->user();
        }
        
        // Only allow editing users in same outlet
        if ($user->outlet_id !== auth()->user()->outlet_id) {
            abort(403, 'Unauthorized access to user.');
        }
        
        // Only owner can change other owner passwords
        if ($user->role === 'owner' && auth()->user()->role !== 'owner' && $user->id !== auth()->id()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Only owners can change owner passwords.');
        }
        
        // Current user can only change their own password with current password validation
        if ($user->id === auth()->id()) {
            $validated = $request->validate([
                'current_password' => ['required', 'current_password'],
                'password' => ['required', 'confirmed', 'min:8'],
            ]);
        } else {
            // Owners can change other users' passwords without current password
            $validated = $request->validate([
                'password' => ['required', 'confirmed', 'min:8'],
            ]);
        }
        
        $user->update([
            'password' => Hash::make($validated['password']),
        ]);
        
        // Log activity
        UserActivity::log(
            'password_change',
            $user->id === auth()->id() ? 'Changed own password' : 'Password changed by admin',
            ['changed_by' => auth()->id(), 'target_user' => $user->id]
        );
        
        // Redirect to profile if changing own password, otherwise back to users list
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.profile')
                ->with('success', 'Password changed successfully.');
        }
        
        return redirect()->back()
            ->with('success', 'Password changed successfully.');
    }
    
    public function profile()
    {
        $user = auth()->user();
        
        // Check if user is authenticated
        if (!$user) {
            return redirect()->route('login')
                ->with('error', 'You must be logged in to access your profile.');
        }
        
        // Get recent activities for this user
        $activities = UserActivity::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
            
        return view('admin.users.profile', compact('user', 'activities'));
    }
    
    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        
        // Check if user is authenticated
        if (!$user) {
            return redirect()->route('login')
                ->with('error', 'You must be logged in to update your profile.');
        }
        
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone' => ['nullable', 'string', 'max:20'],
        ]);
        
        // Log the change
        $changes = [];
        foreach ($validated as $key => $value) {
            if ($user->$key !== $value) {
                $changes[] = $key;
            }
        }
        
        $user->update($validated);
        
        // Log activity
        UserActivity::log(
            'profile_update',
            'Updated profile information: ' . implode(', ', $changes),
            ['fields_updated' => $changes]
        );
        
        return redirect()->route('admin.profile')
            ->with('success', 'Profile updated successfully.');
    }
    
    public function updateAvatar(Request $request)
    {
        $user = auth()->user();
        
        // Check if user is authenticated
        if (!$user) {
            return redirect()->route('login')
                ->with('error', 'You must be logged in to update your avatar.');
        }
        
        $validated = $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);
        
        // Delete old avatar if exists
        if ($user->avatar && Storage::exists($user->avatar)) {
            Storage::delete($user->avatar);
        }
        
        // Store new avatar
        $avatarPath = $request->file('avatar')->store('avatars', 'public');
        
        $user->update([
            'avatar' => $avatarPath,
        ]);
        
        // Log activity
        UserActivity::log(
            'avatar_update',
            'Updated profile photo',
            ['avatar_path' => $avatarPath]
        );
        
        return redirect()->route('admin.profile')
            ->with('success', 'Profile photo updated successfully.');
    }
}
