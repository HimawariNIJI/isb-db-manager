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
            ->paginate(10)
            ->withQueryString();

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
                TO '$username'@'%' WITH GRANT OPTION"
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
    public function downloadTemplate()
    {
        $filename = 'template_mahasiswa.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () {

            $file = fopen('php://output', 'w');

            // UTF-8 BOM agar Excel membaca CSV dengan benar
            fprintf(
                $file,
                chr(0xEF) . chr(0xBB) . chr(0xBF)
            );

            // Header
            fputcsv($file, [
                'nim',
                'nama',
                'email',
                'kelas',
            ]);

            // Contoh data
            fputcsv($file, [
                '0706022410098',
                'Budi',
                'budi@student.uc.ac.id',
                'Database A',
            ]);

            fputcsv($file, [
                '0706022410099',
                'Andi',
                'andi@student.uc.ac.id',
                'Database B',
            ]);

            fclose($file);
        };

        return response()->stream(
            $callback,
            200,
            $headers
        );
    }
    public function import(Request $request)
    {

        $request->validate([
            'csv_file' => [
                'required',
                'file',
                'mimes:csv,txt',
                'max:2048',
            ],
        ]);

        $file = $request->file('csv_file');

        $handle = fopen($file->getRealPath(), 'r');

        if (!$handle) {
            return back()->with(
                'error',
                'File CSV tidak dapat dibaca.'
            );
        }

        try {

            /*
            |--------------------------------------------------------------------------
            | Ambil header
            |--------------------------------------------------------------------------
            */

            $header = fgetcsv($handle);

            if (!$header) {
                fclose($handle);

                return back()->with(
                    'error',
                    'File CSV kosong.'
                );
            }


            /*
            |--------------------------------------------------------------------------
            | Normalisasi header
            |--------------------------------------------------------------------------
            */

            $header = array_map(function ($value) {

                // Hapus UTF-8 BOM
                $value = preg_replace('/^\xEF\xBB\xBF/', '', $value);

                return strtolower(trim($value));
            }, $header);

            $expectedHeader = [
                'nim',
                'nama',
                'email',
                'kelas',
            ];


            if ($header !== $expectedHeader) {

                fclose($handle);

                return back()->with(
                    'error',
                    'Format CSV tidak sesuai. Gunakan nim,nama,email,kelas.'
                );
            }


            $successCount = 0;
            $errors = [];


            /*
            |--------------------------------------------------------------------------
            | Proses setiap mahasiswa
            |--------------------------------------------------------------------------
            */

            while (($row = fgetcsv($handle)) !== false) {

                // Lewati baris kosong
                if (
                    count($row) === 1 &&
                    trim($row[0]) === ''
                ) {
                    continue;
                }

                if (count($row) < 4) {

                    $errors[] =
                        'Ada baris CSV yang tidak memiliki 4 kolom.';

                    continue;
                }


                $nim = trim($row[0]);
                $nama = trim($row[1]);
                $email = trim($row[2]);
                $kelas = trim($row[3]);


                /*
                |--------------------------------------------------------------------------
                | Validasi
                |--------------------------------------------------------------------------
                */

                if ($nim === '' || $nama === '') {

                    $errors[] =
                        'Data NIM atau Nama kosong.';

                    continue;
                }


                /*
                |--------------------------------------------------------------------------
                | Cek NIM sudah ada di Laravel
                |--------------------------------------------------------------------------
                */

                if (Student::where('nim', $nim)->exists()) {

                    $errors[] =
                        "NIM {$nim} sudah terdaftar.";

                    continue;
                }


                /*
                |--------------------------------------------------------------------------
                | Prepare MySQL data
                |--------------------------------------------------------------------------
                */

                $databaseName =
                    'student_' . $nim;

                $safeDatabaseName =
                    preg_replace(
                        '/[^a-zA-Z0-9_]/',
                        '',
                        $databaseName
                    );

                $mysqlUsername =
                    $nim;

                $mysqlPassword =
                    Str::random(12);


                /*
                |--------------------------------------------------------------------------
                | Initialize variables
                |--------------------------------------------------------------------------
                */

                $mysql = null;

                $databaseCreated = false;

                $userCreated = false;


                try {

                    /*
                    |--------------------------------------------------------------------------
                    | Koneksi MySQL Lab
                    |--------------------------------------------------------------------------
                    */

                    $mysql = DB::connection('mysql_lab');

                    $pdo = $mysql->getPdo();

                    /*
                    |--------------------------------------------------------------------------
                    | CREATE DATABASE
                    |--------------------------------------------------------------------------
                    */

                    $mysql->statement(
                        "CREATE DATABASE `$safeDatabaseName`"
                    );

                    $databaseCreated = true;


                    /*
                    |--------------------------------------------------------------------------
                    | CREATE USER
                    |--------------------------------------------------------------------------
                    */

                    $quotedUsername = $pdo->quote($mysqlUsername);
                    $quotedPassword = $pdo->quote($mysqlPassword);

                    $createUserSql =
                        "CREATE USER {$quotedUsername}@'%' IDENTIFIED BY {$quotedPassword}";

                    $mysql->statement($createUserSql);

                    $userCreated = true;

                    /*
                    |--------------------------------------------------------------------------
                    | GRANT ACCESS
                    |--------------------------------------------------------------------------
                    */

                    $mysql->statement(
                        "GRANT ALL PRIVILEGES
                        ON `$safeDatabaseName`.*
                        TO {$quotedUsername}@'%' WITH GRANT OPTION"
                    );


                    /*
                    |--------------------------------------------------------------------------
                    | FLUSH PRIVILEGES
                    |--------------------------------------------------------------------------
                    */

                    $mysql->statement(
                        "FLUSH PRIVILEGES"
                    );


                    /*
                    |--------------------------------------------------------------------------
                    | Simpan ke database Laravel
                    |--------------------------------------------------------------------------
                    */

                    Student::create([
                        'nim' =>
                        $nim,

                        'nama' =>
                        $nama,

                        'email' =>
                        $email ?: null,

                        'kelas' =>
                        $kelas ?: null,

                        'mysql_database' =>
                        $safeDatabaseName,

                        'mysql_username' =>
                        $mysqlUsername,

                        'mysql_password' =>
                        $mysqlPassword,
                    ]);


                    $successCount++;
                } catch (\Throwable $e) {

                    /*
                    |--------------------------------------------------------------------------
                    | Cleanup jika proses gagal
                    |--------------------------------------------------------------------------
                    */

                    if ($mysql !== null) {

                        /*
                        |------------------------------------------------------------------
                        | Hapus user MySQL jika sudah dibuat
                        |------------------------------------------------------------------
                        */

                        if ($userCreated) {

                            try {

                                $quotedUsername =
                                    $mysql->getPdo()->quote($mysqlUsername);

                                $mysql->statement(
                                    "DROP USER IF EXISTS {$quotedUsername}@'%'"
                                );
                            } catch (\Throwable $cleanupException) {

                                // Abaikan error cleanup
                            }
                        }


                        /*
                        |------------------------------------------------------------------
                        | Hapus database jika sudah dibuat
                        |------------------------------------------------------------------
                        */

                        if ($databaseCreated) {

                            try {

                                $mysql->statement(
                                    "DROP DATABASE IF EXISTS `$safeDatabaseName`"
                                );
                            } catch (\Throwable $cleanupException) {

                                // Abaikan error cleanup
                            }
                        }
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | Simpan error
                    |--------------------------------------------------------------------------
                    */

                    $errors[] =
                        "NIM {$nim}: " . $e->getMessage();
                }
            }


            fclose($handle);


            /*
            |--------------------------------------------------------------------------
            | Hasil
            |--------------------------------------------------------------------------
            */

            if ($successCount === 0) {

                return back()
                    ->with(
                        'error',
                        'Tidak ada mahasiswa yang berhasil diimport.'
                    )
                    ->with(
                        'import_errors',
                        $errors
                    );
            }


            return redirect()
                ->route('students.index')
                ->with(
                    'success',
                    "{$successCount} mahasiswa berhasil diimport."
                )
                ->with(
                    'import_errors',
                    $errors
                );
        } catch (\Exception $e) {

            fclose($handle);

            return back()->with(
                'error',
                'Gagal melakukan import: ' . $e->getMessage()
            );
        }
    }
    public function create()
    {
        return view('students.create');
    }

    public function importPage()
    {
        return view('students.import');
    }
}
