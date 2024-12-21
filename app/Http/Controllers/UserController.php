<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cookie;

class UserController extends Controller
{
    /**
     * Instantiate a new UserController instance.
     */
    public function __construct()
    {
        $this->middleware('guest')->except([
            'logout',
        ]);
    }

    /**
     * Display a registration form.
     *
     * @return \Illuminate\Http\Response
     */
    public function register()
    {
        return view('register');
    }

    /**
     * Store a new user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:250',
            'email' => 'required|email|regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/|max:250|unique:users',
            'password' => ['required', 'min:8', 'regex:/[A-Z]/',  'regex:/[a-z]/',  'regex:/[0-9]/', 'regex:/[^a-zA-Z0-9]/'],
        ], [
            'password.regex' => 'The password must include at least one uppercase letter, one lowercase letter, one number, and one special character.',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'user_status' => 1,
        ]);

        $credentials = $request->only('email', 'password');
        Auth::attempt($credentials);
        $request->session()->regenerate();
        return redirect('/')
            ->withSuccess('You have successfully registered & logged in!');
    }

    /**
     * Display a login form.
     *
     * @return \Illuminate\Http\Response
     */
    public function login()
    {
        return view('login');
    }

    /**
     * Authenticate the user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function authenticate(Request $request)
    {
        $request->validate([
            'email' => 'required|email|regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', // Email format check
            'password' => ['required', 'min:8', 'regex:/[A-Z]/',  'regex:/[a-z]/',  'regex:/[0-9]/', 'regex:/[^a-zA-Z0-9]/'],
        ], [
            'password.regex' => 'The password must include at least one uppercase letter, one lowercase letter, one number, and one special character.',
        ]);

        $attemp = [
            'email' => $request->email,
            'password' => $request->password,
            'user_status' => 1,
            'is_admin' => 0,
        ];

        // Attempt to authenticate the user
        if (Auth::attempt($attemp)) {
            $request->session()->regenerate();
            if ($request->has('remember-me')) {
                // Save the email in a cookie for 15 days
                Cookie::queue('user_email', $request->email, 60 * 24 * 15);
            } else {
                // Remove the cookie if "Remember Me" is unchecked
                Cookie::queue(Cookie::forget('user_email'));
            }

            return redirect('/')
                ->withSuccess('You have successfully logged in!');
        } else {
            Auth::logout();
            return back()->withErrors([
                'email' => 'You are not authorized to access this area.',
            ])->onlyInput('email');
        }

        return back()->withErrors([
            'email' => 'Your provided credentials do not match in our records.',
        ])->onlyInput('email');
    }

    /**
     * Log out the user from application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/')
            ->withSuccess('You have logged out successfully!');;
    }
}
