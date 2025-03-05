<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('checkRole:admin'); 
    }

    /**
     * Admin Dashboard
     */
    public function dashboard()
    {
        return view('admin.dashboard');
    }

    /**
     * Manage Users (View List of Workers & Admins)
     */
    public function manageUsers()
    {
        $users = User::whereIn('role', ['worker', 'admin'])->get(); // Fetch both workers & admins
        return view('admin.users.index', compact('users'));
    }

    /**
     * Create a New Worker or Admin
     */
    public function createUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'phone' => 'required|unique:users',
            'password' => 'required|min:6',
            'role' => 'required|in:admin,worker', 
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => bcrypt($request->password),
            'role' => $request->role, 
        ]);

        return redirect()->route('admin.users')->with('success', ucfirst($request->role) . ' created successfully.');
    }

    /**
     * Delete a Worker or Admin (Admin Cannot Delete Themselves)
     */
    public function deleteUser($id)
    {
        $user = User::findOrFail($id);

        // Prevent deleting self
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users')->with('error', 'You cannot delete your own account.');
        }

        // Prevent deleting non-admin/worker roles
        if (!in_array($user->role, ['admin', 'worker'])) {
            return redirect()->route('admin.users')->with('error', 'Invalid user role.');
        }

        $user->delete();
        return redirect()->route('admin.users')->with('success', ucfirst($user->role) . ' deleted successfully.');
    }
}
