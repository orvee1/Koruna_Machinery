<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = Auth::user();

        if (!$user) {
            // ইউজার লগিন করা নাই
            abort(403, 'Unauthorized. User not logged in.');
        }

        if (!in_array($user->role, $roles)) {
            // ইউজারের রোল ম্যাচ করছে না
            abort(403, 'Unauthorized. You do not have access.');
        }

        return $next($request);
    }
}
