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

        try {
            // 2. Buat Database Baru di Host Lab
            $mysql = DB::connection('mysql_lab');
            $pdo = $mysql->getPdo();

            $mysql->statement("CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");

            foreach ($selectedStudents as $student) {
                $username = trim($student->mysql_username);
                if (!$username) continue;

                // Cari host yang sudah ada untuk user ini di server MySQL
                $userHosts = $mysql->select("SELECT Host FROM mysql.user WHERE User = ?", [$username]);

                if (empty($userHosts)) {
                    // Jika tidak ada entri user di server MySQL, buat hanya user@'%'
                    // Menghindari pembuatan host spesifik yang menyebabkan duplikasi.
                    $pwd = $student->mysql_password ?? '';
                    $quotedPwd = $pdo->quote($pwd);
                    $hostsToCreate = ['%'];
                    foreach ($hostsToCreate as $h) {
                        $mysql->statement("CREATE USER IF NOT EXISTS '{$username}'@'{$h}' IDENTIFIED BY {$quotedPwd}");
                    }
                    $hosts = $hostsToCreate;
                } else {
                    // Gunakan host yang sudah ada dan jangan buat host baru
                    $hosts = array_map(fn($r) => $r->Host, $userHosts);
                }

                // Berikan GRANT pada setiap host yang relevan
                foreach ($hosts as $host) {
                    $mysql->statement("GRANT ALL PRIVILEGES ON `{$dbName}`.* TO '{$username}'@'{$host}'");
                }

                $createdCredentials[] = [
                    'nim'      => $student->nim,
                    'nama'     => $student->nama,
                    'username' => $username,
                    'password' => $student->mysql_password ?? null,
                ];
            }

            $mysql->statement("FLUSH PRIVILEGES;");

            // 4. Simpan ke database lokal (tidak menyimpan password kelompok)
            $groupDb = GroupDatabase::create([
                'database_name' => $dbName,
                'password'      => null,
            ]);
            $groupDb->students()->sync($request->students);

            return redirect()->route('group-databases.create')
                ->with('success', "Database kelompok '{$dbName}' berhasil dibuat!")
                ->with('credentials', $createdCredentials)
                ->with('db_name', $dbName)
                ->with('group_created', true);
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

            $mysql = DB::connection('mysql_lab');
            $pdo = $mysql->getPdo();

            $mysql->statement("DROP DATABASE IF EXISTS `{$dbName}`;");

            foreach ($groupDb->students as $student) {
                $username = $student->mysql_username;
                if (!$username) continue;

                $quotedUsername = $pdo->quote($username);

                // Determine existing hosts for this user; if none, try common hosts
                $userHosts = $mysql->select("SELECT Host FROM mysql.user WHERE User = ?", [$username]);
                if (!empty($userHosts)) {
                    $hosts = array_map(fn($r) => $r->Host, $userHosts);
                } else {
                    // Jika tidak ada host sama sekali, fallback hanya ke '%'
                    $hosts = ['%'];
                }

                foreach ($hosts as $host) {
                    $exists = $mysql->select("SELECT 1 FROM mysql.user WHERE User = ? AND Host = ? LIMIT 1", [$username, $host]);
                    if (empty($exists)) continue;

                    try {
                        $mysql->statement("REVOKE ALL PRIVILEGES ON `{$dbName}`.* FROM {$quotedUsername}@'{$host}';");
                    } catch (\Exception $e) {
                        $msg = $e->getMessage();
                        if (stripos($msg, '1141') !== false || stripos($msg, 'no such grant') !== false) {
                            continue;
                        }
                        throw $e;
                    }
                }
            }

            $mysql->statement("FLUSH PRIVILEGES;");

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
