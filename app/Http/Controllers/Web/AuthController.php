<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    /**
     * Show login form
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Process login
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (!auth()->guard('admin')->attempt($credentials)) {
            return back()
                ->withInput()
                ->withErrors(['email' => 'Email atau kata sandi tidak valid.']);
        }

        $request->session()->regenerate();

        return redirect()->route('admin.dashboard');
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        auth()->guard('admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}