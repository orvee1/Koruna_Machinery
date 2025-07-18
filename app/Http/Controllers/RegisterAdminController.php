<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class RegisterAdminController extends Controller
{
    public function showForm()
    {
        return view('register-admin');
    }

    public function store(Request $request)
    {
        $user = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|unique:users,phone',
            'email' => 'required|string|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        User::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => 'admin',
        ]);

        if ($user) {
            return redirect()->route('auth.login')->with('success', 'Admin registered successfully!');
        } else {
            return back()->with('error', 'Failed to register admin.');
        }
    }
}
