<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    // Domain email universitas yang diizinkan
    protected $allowedDomains = [
        '@student.ciputra.ac.id',
    ];

    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            $email = strtolower($googleUser->getEmail());

            // 1. CEK VALIDASI DOMAIN EMAIL
            if (!Str::endsWith($email, $this->allowedDomains)) {
                return redirect()->route('userlogin')->with('error', 'Akses ditolak! Anda harus menggunakan email resmi universitas untuk login.');
            }

            // 2. JIKA EMAIL SESUAI, DAFARKAN / LOGIN-KAN USER
            $user = User::where('email', $email)->first();

            if (!$user) {
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $email,
                    'google_id' => $googleUser->getId(),
                    'password' => null, // User login via Google
                ]);
            } else {
                // Update Google ID jika sebelumnya belum ada
                $user->update(['google_id' => $googleUser->getId()]);
            }

            // 3. LOGIN KAN USER & REDIRECT KE DASHBOARD
            Auth::login($user);

            return redirect()
                ->route('user.dashboard')
                ->with(
                    'success',
                    'Berhasil login menggunakan email universitas!'
                );
        } catch (\Exception $e) {
            return redirect()->route('userlogin')->with('error', 'Gagal melakukan login dengan Google: ' . $e->getMessage());
        }
    }
}
