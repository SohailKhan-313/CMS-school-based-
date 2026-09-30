<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    // Show login form
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Handle login
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($request->only('email', 'password'))) {

            $request->session()->regenerate();

            // Redirect based on role
            if (auth()->user()->hasRole('admin')) {
                return redirect()->route('home');
            }

            if (auth()->user()->hasRole('teacher')) {
                return redirect()->route('teachers.index');
            }

            if (auth()->user()->hasRole('accountant')) {
                return redirect()->route('accountant.index');
            }

            if (auth()->user()->hasRole('student')) {
                return redirect()->route('students.index');
            }

            return redirect()->route('home');
        }

        return back()->withErrors([
            'email' => 'Invalid credentials. Please verify your email and password.',
        ]);
    }

    // Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
