<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ManageUsersController extends Controller
{
    public function index()
    {
        // Paginate 15 users per page
        $users = User::latest()->paginate(15);

        return view('admin.manage-users', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|unique:users,username|max:50',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'phone' => 'nullable|string|max:20',
        ]);

        User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'role' => 'staff', // Always staff — admins cannot create other admins from UI
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'status' => 'active',
            'theme' => 'light',
        ]);

        return back()->with('success', 'Staff account created successfully.');
    }

    public function toggleStatus(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot deactivate your own account.');
        }

        $user->update([
            'status' => $user->status === 'active' ? 'inactive' : 'active',
        ]);

        return back()->with('success', 'User status updated.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot archive your own account.');
        }

        if ($user->role === 'admin') {
            return back()->with('error', 'Admin accounts cannot be archived from this panel.');
        }

        $user->delete();

        return back()->with('success', 'User archived successfully.');
    }
}
