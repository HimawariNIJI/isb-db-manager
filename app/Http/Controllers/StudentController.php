<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $students = Student::query()
            ->when($search, function ($query, $search) {

                $query->where('nim', 'like', '%' . $search . '%')
                    ->orWhere('nama', 'like', '%' . $search . '%');

            })
            ->orderBy('created_at', 'desc')
            ->get();

        return view('students.index', compact('students', 'search'));
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nim' => 'required|string|max:20|unique:students,nim',
            'nama' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'kelas' => 'nullable|string|max:50',
        ]);

        $nim = $validated['nim'];

        $databaseName = 'student_' . $nim;

        $username = $nim;

        $password = Str::random(12);

        $databaseCreated = false;
        $userCreated = false;

        try {

            /*
            |--------------------------------------------------------------------------
            | 1. CREATE DATABASE
            |--------------------------------------------------------------------------
            */

            DB::connection('mysql_lab')->statement(
                "CREATE DATABASE `$databaseName`"
            );

            $databaseCreated = true;


            /*
            |--------------------------------------------------------------------------
            | 2. CREATE USER
            |--------------------------------------------------------------------------
            */

            DB::connection('mysql_lab')->statement(
                "CREATE USER '$username'@'%' IDENTIFIED BY '$password'"
            );

            $userCreated = true;


            /*
            |--------------------------------------------------------------------------
            | 3. GRANT
            |--------------------------------------------------------------------------
            */

            DB::connection('mysql_lab')->statement(
                "GRANT ALL PRIVILEGES
                ON `$databaseName`.*
                TO '$username'@'%'"
            );


            /*
            |--------------------------------------------------------------------------
            | 4. SAVE STUDENT
            |--------------------------------------------------------------------------
            */

            Student::create([
                'nim' => $validated['nim'],
                'nama' => $validated['nama'],
                'email' => $validated['email'] ?? null,
                'kelas' => $validated['kelas'] ?? null,

                'mysql_database' => $databaseName,
                'mysql_username' => $username,
                'mysql_password' => $password,
            ]);


            /*
            |--------------------------------------------------------------------------
            | 5. SUCCESS
            |--------------------------------------------------------------------------
            */

            return redirect()
                ->route('students.index')
                ->with('success', 'Mahasiswa berhasil ditambahkan.');


        } catch (\Exception $e) {


            /*
            |--------------------------------------------------------------------------
            | ROLLBACK MYSQL
            |--------------------------------------------------------------------------
            */

            if ($userCreated) {

                try {

                    DB::connection('mysql_lab')->statement(
                        "DROP USER '$username'@'%'"
                    );

                } catch (\Exception $rollbackError) {
                    // Abaikan error rollback
                }
            }


            if ($databaseCreated) {

                try {

                    DB::connection('mysql_lab')->statement(
                        "DROP DATABASE `$databaseName`"
                    );

                } catch (\Exception $rollbackError) {
                    // Abaikan error rollback
                }
            }


            return back()
                ->withInput()
                ->with(
                    'error',
                    'Gagal membuat akun mahasiswa: ' . $e->getMessage()
                );
        }
    }
    public function show(Student $student)
    {
        return view('students.show', compact('student'));
    }

    public function destroy(Student $student)
    {
        $databaseName = $student->mysql_database;
        $username = $student->mysql_username;

        try {

            /*
            |--------------------------------------------------------------------------
            | 1. HAPUS MYSQL USER
            |--------------------------------------------------------------------------
            */

            DB::connection('mysql_lab')->statement(
                "DROP USER IF EXISTS '$username'@'%'"
            );


            /*
            |--------------------------------------------------------------------------
            | 2. HAPUS DATABASE
            |--------------------------------------------------------------------------
            */

            DB::connection('mysql_lab')->statement(
                "DROP DATABASE IF EXISTS `$databaseName`"
            );


            /*
            |--------------------------------------------------------------------------
            | 3. HAPUS DATA MAHASISWA
            |--------------------------------------------------------------------------
            */

            $student->delete();


            /*
            |--------------------------------------------------------------------------
            | SUCCESS
            |--------------------------------------------------------------------------
            */

            return redirect()
                ->route('students.index')
                ->with(
                    'success',
                    'Mahasiswa, user MySQL, dan database berhasil dihapus.'
                );


        } catch (\Exception $e) {

            return back()
                ->with(
                    'error',
                    'Gagal menghapus mahasiswa: ' . $e->getMessage()
                );
        }
    }

    public function updatePassword(Request $request, Student $student)
    {
        $validated = $request->validate([
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],
        ]);

        try {

            $username = $student->mysql_username;
            $password = $validated['password'];

            DB::connection('mysql_lab')->statement(
                "ALTER USER '$username'@'%' IDENTIFIED BY '$password'"
            );

            $student->update([
                'mysql_password' => $password,
            ]);

            return back()->with(
                'success',
                'Password MySQL berhasil diubah.'
            );

        } catch (\Exception $e) {

            return back()->with(
                'error',
                'Gagal mengubah password: ' . $e->getMessage()
            );
        }
    }
    public function export()
    {
        $students = Student::orderBy('nim')->get();

        $filename = 'data_mahasiswa_' . now()->format('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($students) {

            $file = fopen('php://output', 'w');

            // UTF-8 BOM agar Excel membaca karakter dengan benar
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Header CSV
            fputcsv($file, [
                'NIM',
                'Nama',
                'Email',
                'Kelas',
                'Database MySQL',
                'Username MySQL',
                'Password MySQL',
            ]);

            // Data mahasiswa
            foreach ($students as $student) {

                fputcsv($file, [
                    $student->nim,
                    $student->nama,
                    $student->email ?? '',
                    $student->kelas ?? '',
                    $student->mysql_database,
                    $student->mysql_username,
                    $student->mysql_password,
                ]);

            }

            fclose($file);
        };

        return response()->stream(
            $callback,
            200,
            $headers
        );
    }
}