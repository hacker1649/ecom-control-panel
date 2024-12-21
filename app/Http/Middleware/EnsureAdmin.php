<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if the user is logged in and has admin privileges
        if (Auth::check() && Auth::user()->is_admin == 1) {
            return $next($request);
        }

        // If the user is logged in but not an admin, redirect them to the appropriate e-commerce page
        if (Auth::check() && Auth::user()->is_admin == 0) {
            return redirect('/')->with('success', 'Logged in as admin.');
        }

        return redirect()->route('a_login')->with('error', 'Access denied. Admins only.');
    }
}
