<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Branch;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('checkRole:admin'); // ✅ Now using checkRole middleware cleanly
    }

    public function dashboard()
    {
        $users = User::with('branch')->get();
        return view('admin.dashboard', compact('users'));
    }

    public function indexUsers()
    {
        $users = User::with('branch')->latest()->paginate(20);
        return view('admin.users.index', compact('users'));
    }

    public function createUser()
    {
        $branches = Branch::all();
        return view('admin.users.create', compact('branches'));
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'phone' => 'required|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'password_confirmation' => 'required|string|min:6',
            'role' => 'required|in:admin,worker,manager',
            'branch_id' => 'nullable|exists:branches,id',
        ]);

        if ($request->role === 'admin') {
            $request->merge(['branch_id' => null]);
        }

        User::create($request->all());

        return redirect()->route('admin.dashboard')->with('success', 'User created successfully!');
    }

    public function editUser(User $user)
    {
        $branches = Branch::all();
        return view('admin.users.edit', compact('user', 'branches'));
    }

    public function updateUser(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'required|unique:users,phone,' . $user->id,
            'password' => 'nullable|string|min:6|confirmed',
            'password_confirmation' => 'nullable|string|min:6',
            'role' => 'required|in:admin,worker,manager',
            'branch_id' => 'nullable|exists:branches,id',
        ]);

        if ($request->has('password') && $request->password) {
            $user->password = bcrypt($request->password);
        }

        if ($request->role === 'admin') {
            $request->merge(['branch_id' => null]);
        }

        $user->update($request->all());

        return redirect()->route('admin.dashboard')->with('success', 'User updated successfully!');
    }
}
