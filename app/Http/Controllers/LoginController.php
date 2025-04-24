<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    protected function authenticated(Request $request, $user)
{
    if ($user->role === 'admin') {
        return redirect()->route('admin.select-branch');
    }

    session(['active_branch_id' => $user->branch_id]);

    return redirect()->route('dashboard');
}
}
