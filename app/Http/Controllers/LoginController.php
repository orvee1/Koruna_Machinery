<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'phone'    => 'required|string|digits_between:10,15',
            'password' => 'required|string|min:6',
        ]);

        $throttleKey = 'login-attempts:' . $request->ip();
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            throw ValidationException::withMessages([
                'phone' => "Too many login attempts. Please try again in {$seconds} seconds.",
            ])->status(429);
        }

        if (Auth::attempt($credentials)) {

            RateLimiter::clear($throttleKey);

            $request->session()->regenerate();
            return $this->authenticated($request, Auth::user());
        }

        RateLimiter::hit($throttleKey, 60);

        return back()->withErrors([
            'phone' => __('auth.failed'),
        ])->onlyInput('phone');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->with('status', 'You have been logged out.');
    }

    protected function authenticated(Request $request, $user)
    {
        if ($user->role === 'admin') {
            return redirect()->route('admin.select-branch');
        }

        session(['active_branch_id' => $user->branch_id]);

        if ($user->role === 'manager') {
            return redirect()->route('manager.dashboard');
        }
        if ($user->role === 'worker') {
            return redirect()->route('worker.sales.index');
        }

        abort(403, 'Unauthorized Role');
    }
}
