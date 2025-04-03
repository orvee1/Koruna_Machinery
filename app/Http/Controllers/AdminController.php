<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        $users = User::all();
        return view('admin.dashboard', compact('users'));
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
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required|string|min:8',
            'role' => 'required|in:admin,worker,manager',
            'branch_id' => 'required|exists:branches,id',
        ]);

        User::create($request->all());
        return redirect()->route('admin.users.index')->with('success', 'User created successfully!');
    }
}
