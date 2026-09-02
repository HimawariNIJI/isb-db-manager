<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class IsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Cek login admin hardcode dari session
        $isHardcodedAdmin = session('admin_logged_in') === true;

        // 2. Cek login admin dari database / Google OAuth
        $isDbAdmin = Auth::check() && (int) Auth::user()->is_admin === 1;

        // Jika salah satu bernilai true, izinkan masuk
        if ($isHardcodedAdmin || $isDbAdmin) {
            return $next($request);
        }

        // Jika tidak dua-duanya, kembalikan ke halaman login
        return redirect()->route('login')->with('error', 'Akses ditolak. Silakan login sebagai admin.');
    }
}
