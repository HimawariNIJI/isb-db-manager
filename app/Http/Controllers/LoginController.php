<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function showUserLogin()
    {
        return view('auth.userlogin');
    }

    public function showAdminLogin()
    {
        return view('auth.login');
    }

    public function adminLogin(Request $request)
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

    public function logoutAdmin(Request $request)
    {
        $request->session()->forget('admin_logged_in');

        return redirect()->route('login');
    }

    public function logoutUser(Request $request)
    {
        $request->session()->forget('user_logged_in');

        return redirect()->route('userlogin');
    }
}
