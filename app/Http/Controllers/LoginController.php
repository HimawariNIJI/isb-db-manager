<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        if (
            $credentials['username'] === 'admin' &&
            $credentials['password'] === 'admin123'
        ) {
            session(['admin_logged_in' => true]);

            return redirect()->route('dashboard');
        }

        return back()
            ->withInput($request->only('username'))
            ->with('error', 'Username atau password salah.');
    }

    public function logout(Request $request)
    {
        $request->session()->forget('admin_logged_in');

        return redirect()->route('login');
    }
}
