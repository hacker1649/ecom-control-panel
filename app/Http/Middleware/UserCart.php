<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class UserCart
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if the user is authenticated and an admin
        if (Auth::check()) {
            // check if the logged member is an admin
            if (Auth::user()->is_admin == 1) {
                // Log the admin out
                Auth::logout();

                // Redirect to the login page with a message
                return redirect()->route('login')->with('error', 'Admins are not allowed to access the cart.');
            } else {
                // If the user is not an admin, proceed to the next request
                return $next($request);
            }
        }

        // Redirect to the login page with a message
        return redirect()->route('login')->with('error', 'Please login to access the cart.');
    }
}
