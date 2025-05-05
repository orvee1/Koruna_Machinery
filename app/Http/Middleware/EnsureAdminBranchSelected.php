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
        // Admin রোল চেক করছি
        if (Auth::check() && Auth::user()->role === 'admin') {
            // সেশন চেক করি যে ব্রাঞ্চ সিলেক্ট করা হয়েছে কিনা
            if (!session()->has('selected_branch_id')) {
                // যদি না থাকে, সিলেক্ট ব্রাঞ্চ পেইজে রিডিরেক্ট করবো
                return redirect()->route('admin.select-branch');
            }
        }
    
        // সেশন থাকলে ফলো আপ করি
        return $next($request);
    }
    
}
