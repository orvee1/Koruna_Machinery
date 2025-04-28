<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminBranchSelected
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        if (Auth::check() && Auth::user()->role === 'admin') {
            if (!session()->has('active_branch_id')) {
                // Exception routes where branch selection not required
                if (!$request->is('admin/select-branch') && 
                    !$request->is('admin/select-branch/*') &&
                    !$request->is('admin/branches') &&
                    !$request->is('admin/branches/*')) 
                {
                    return redirect()->route('admin.select-branch');
                }
            }
        }

        return $next($request);
    }
}
