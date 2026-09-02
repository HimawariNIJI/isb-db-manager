<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserDashboardController extends Controller
{
    /**
     * Dashboard mahasiswa
     */
    public function index()
    {
        $user = Auth::user();

        // Cari data mahasiswa berdasarkan email login & sertakan relasi groups + members
        $student = Student::with(['groups.members'])
            ->where('email', $user->email)
            ->first();

        if (!$student) {
            Auth::logout();

            return redirect()->route('userlogin')
                ->with('error', 'Data mahasiswa tidak ditemukan.');
        }

        return view('user.dashboard', compact('student'));
    }

    /**
     * Ubah password MySQL
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],
        ], [
            'password.required' => 'Password baru wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $user = Auth::user();

        // Cari mahasiswa berdasarkan email
        $student = Student::where('email', $user->email)->first();

        if (!$student) {
            return redirect()->route('user.dashboard')
                ->with('error', 'Data mahasiswa tidak ditemukan.');
        }

        if (!$student->mysql_username) {
            return redirect()->route('user.dashboard')
                ->with('error', 'Username database mahasiswa tidak ditemukan.');
        }

        $newPassword = $request->password;
        $username = $student->mysql_username;

        try {

            /*
            |--------------------------------------------------------------------------
            | UBAH PASSWORD USER MYSQL
            |--------------------------------------------------------------------------
            */

            $mysql = DB::connection('mysql_lab');
            $pdo = $mysql->getPdo();

            $quotedUsername = $pdo->quote($username);
            $quotedPassword = $pdo->quote($newPassword);

            $mysql->statement(
                "ALTER USER {$quotedUsername}@'%' IDENTIFIED BY {$quotedPassword}"
            );

            /*
            |--------------------------------------------------------------------------
            | UPDATE PASSWORD DI DATABASE LARAVEL
            |--------------------------------------------------------------------------
            */

            $student->update([
                'mysql_password' => $newPassword,
            ]);

            /*
            |--------------------------------------------------------------------------
            | FLUSH PRIVILEGES
            |--------------------------------------------------------------------------
            */

            $mysql->statement('FLUSH PRIVILEGES');

            return redirect()
                ->route('user.dashboard')
                ->with('success', 'Password database berhasil diubah.');
        } catch (\Throwable $e) {

            return redirect()
                ->route('user.dashboard')
                ->with(
                    'error',
                    'Gagal mengubah password database: ' . $e->getMessage()
                );
        }
    }
}
