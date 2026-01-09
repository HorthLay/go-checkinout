<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Show the login form
     */
    public function showLoginForm()
    {
        // Redirect if already authenticated
        if (Auth::check()) {
            return redirect()->route('home');
        }

        return view('auth.login');
    }

    /**
     * Handle login request
     */
    public function login(Request $request)
    {
        // Validate input
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string|min:8',
        ]);

        // Find user by username or email
        $user = User::where('name', $request->login)
            ->orWhere('email', $request->login)
            ->first();

        // Check if user exists
        if (!$user) {
            throw ValidationException::withMessages([
                'login' => 'These credentials do not match our records.',
            ]);
        }

        // Check if password matches
        if (!Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'password' => 'The password is incorrect.',
            ]);
        }

        // Check if user is active
        if (!$user->active) {
            throw ValidationException::withMessages([
                'login' => 'Your account has been deactivated. Please contact support.',
            ]);
        }

        // Log the user in
        Auth::login($user, $request->filled('remember'));

        // Regenerate session to prevent fixation attacks
        $request->session()->regenerate();

        // Redirect to intended page or home
        return redirect()->intended(route('home'))->with('success', 'Welcome back, ' . $user->name . '!');
    }

    /**
     * Handle logout request
     */
    public function logout(Request $request)
    {
        Auth::logout();

        // Invalidate the session
        $request->session()->invalidate();

        // Regenerate CSRF token
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'You have been logged out successfully.');
    }
}