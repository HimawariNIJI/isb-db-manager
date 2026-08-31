<?php

namespace App\Http\Controllers;

use App\Models\GroupDatabase;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GroupDatabaseController extends Controller
{
    public function create(Request $request)
    {
        $students = Student::whereNotNull('mysql_username')
            ->orderBy('nama', 'asc')
            ->get();

        $search = $request->query('search');

        // Mengambil seluruh database kelompok agar tidak ada yang hilang dari tabel
        $groupDatabases = GroupDatabase::with('students')
            ->when($search, function ($query, $search) {
                return $query->where('database_name', 'like', "%{$search}%")
                    ->orWhereHas('students', function ($q) use ($search) {
                        $q->where('nama', 'like', "%{$search}%")
                            ->orWhere('nim', 'like', "%{$search}%");
                    });
            })
            ->latest()
            ->get();

        return view('group_databases.create', compact('students', 'groupDatabases', 'search'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'database_name' => 'required|string|max:64|regex:/^[a-zA-Z0-9_]+$/',
            'students'      => 'required|array|min:1',
            'students.*'    => 'exists:students,id',
        ], [
            'database_name.regex' => 'Nama database hanya boleh berisi huruf, angka, dan underscore (_).',
            'students.required'   => 'Pilih minimal satu mahasiswa untuk kelompok ini.',
        ]);

        $dbName = strtolower(trim($request->database_name));
        $selectedStudents = Student::whereIn('id', $request->students)->get();
        $createdCredentials = [];

        // 1. Generate 1 Password Seragam untuk seluruh user di kelompok ini
        $groupPassword = Str::random(12);

        try {
            // 2. Buat Database Baru di Host Lab
            DB::connection('mysql_lab')->statement("CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");

            foreach ($selectedStudents as $student) {
                $username = trim($student->mysql_username);
                if (!$username) continue;

                // 3. Set password yang sama untuk tiap user kelompok di MySQL Server
                DB::connection('mysql_lab')->statement("CREATE USER IF NOT EXISTS '{$username}'@'%' IDENTIFIED BY '{$groupPassword}';");
                DB::connection('mysql_lab')->statement("ALTER USER '{$username}'@'%' IDENTIFIED BY '{$groupPassword}';");
                DB::connection('mysql_lab')->statement("GRANT ALL PRIVILEGES ON `{$dbName}`.* TO '{$username}'@'%';");

                $createdCredentials[] = [
                    'nim'      => $student->nim,
                    'nama'     => $student->nama,
                    'username' => $username,
                    'password' => $groupPassword,
                ];
            }

            DB::connection('mysql_lab')->statement("FLUSH PRIVILEGES;");

            // 4. Simpan ke database lokal beserta password kelompoknya
            $groupDb = GroupDatabase::create([
                'database_name' => $dbName,
                'password'      => $groupPassword,
            ]);
            $groupDb->students()->sync($request->students);

            return redirect()->route('group-databases.create')
                ->with('success', "Database kelompok '{$dbName}' berhasil dibuat!")
                ->with('credentials', $createdCredentials)
                ->with('db_name', $dbName);
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal membuat database kelompok: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $groupDb = GroupDatabase::with('students')->findOrFail($id);
            $dbName = $groupDb->database_name;

            DB::connection('mysql_lab')->statement("DROP DATABASE IF EXISTS `{$dbName}`;");

            foreach ($groupDb->students as $student) {
                if ($student->mysql_username) {
                    DB::connection('mysql_lab')->statement("REVOKE ALL PRIVILEGES ON `{$dbName}`.* FROM '{$student->mysql_username}'@'%';");
                }
            }
            DB::connection('mysql_lab')->statement("FLUSH PRIVILEGES;");

            $groupDb->students()->detach();
            $groupDb->delete();

            return redirect()->route('group-databases.create')
                ->with('success', "Database kelompok '{$dbName}' berhasil dihapus!");
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus database kelompok: ' . $e->getMessage());
        }
    }
}
