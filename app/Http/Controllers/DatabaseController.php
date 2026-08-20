<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use Illuminate\Support\Facades\DB;

class DatabaseController extends Controller
{
    public function index()
    {
        $databases = Student::all();
        return view('databases.index', compact('databases'));
    }

    public function show(Request $request, $id)
    {
        $database = Student::findOrFail($id);
        $dbName   = trim($database->mysql_database);

        // 1. Ambil hak akses tingkat database (Semua Tabel / *)
        $dbGrants = DB::connection('mysql_lab')->select("
        SELECT User, Host, Select_priv, Insert_priv, Update_priv, Delete_priv, Execute_priv
        FROM mysql.db 
        WHERE LOWER(Db) = LOWER(?)
    ", [$dbName]);

        // 2. Ambil hak akses tingkat tabel spesifik
        $tableGrants = DB::connection('mysql_lab')->select("
        SELECT User, Host, Table_name, Table_priv
        FROM mysql.tables_priv 
        WHERE LOWER(Db) = LOWER(?)
    ", [$dbName]);

        $grantedAccess    = [];
        $grantedUsernames = [];

        // Format data hak akses tingkat database
        foreach ($dbGrants as $grant) {
            $grantedUsernames[] = $grant->User;
            $student = Student::where('mysql_username', $grant->User)->first();

            $privs = [];
            if ($grant->Select_priv === 'Y')  $privs[] = 'SELECT';
            if ($grant->Insert_priv === 'Y')  $privs[] = 'INSERT';
            if ($grant->Update_priv === 'Y')  $privs[] = 'UPDATE';
            if ($grant->Delete_priv === 'Y')  $privs[] = 'DELETE';
            if ($grant->Execute_priv === 'Y') $privs[] = 'EXECUTE';

            $grantedAccess[] = [
                'username'     => $grant->User,
                'host'         => $grant->Host,
                'student_name' => $student ? $student->nama : 'Mahasiswa External',
                'nim'          => $student ? $student->nim : '-',
                'table'        => '*',
                'privileges'   => !empty($privs) ? implode(', ', $privs) : 'ALL PRIVILEGES',
            ];
        }

        // Format data hak akses tingkat tabel spesifik
        foreach ($tableGrants as $grant) {
            $grantedUsernames[] = $grant->User;
            $student = Student::where('mysql_username', $grant->User)->first();

            $grantedAccess[] = [
                'username'     => $grant->User,
                'host'         => $grant->Host,
                'student_name' => $student ? $student->nama : 'Mahasiswa External',
                'nim'          => $student ? $student->nim : '-',
                'table'        => $grant->Table_name,
                'privileges'   => strtoupper(str_replace(',', ', ', $grant->Table_priv)),
            ];
        }

        // 3. LOGIKA FILTER & SORTING (HAK AKSES AKTIF)
        $accessCollection = collect($grantedAccess);

        // Search Keyword
        if ($search = $request->input('search_active')) {
            $accessCollection = $accessCollection->filter(function ($item) use ($search) {
                return str_contains(strtolower($item['student_name']), strtolower($search)) ||
                    str_contains(strtolower($item['nim']), strtolower($search)) ||
                    str_contains(strtolower($item['username']), strtolower($search)) ||
                    str_contains(strtolower($item['privileges']), strtolower($search));
            });
        }

        // Filter berdasarkan Tabel
        if ($tableFilter = $request->input('table_filter')) {
            if ($tableFilter !== 'all') {
                $accessCollection = $accessCollection->where('table', $tableFilter);
            }
        }

        // Sorting Column & Direction
        $sortBy  = $request->input('sort_by', 'student_name');
        $sortDir = $request->input('sort_dir', 'asc');

        if ($sortDir === 'desc') {
            $accessCollection = $accessCollection->sortByDesc($sortBy, SORT_NATURAL | SORT_FLAG_CASE);
        } else {
            $accessCollection = $accessCollection->sortBy($sortBy, SORT_NATURAL | SORT_FLAG_CASE);
        }

        $grantedAccess = $accessCollection->values()->all();

        // 4. Mahasiswa yang belum memilii akses (Pilih User)
        if ($database->mysql_username) {
            $grantedUsernames[] = $database->mysql_username;
        }

        $students = Student::whereNotNull('mysql_username')
            ->whereNotIn('mysql_username', array_unique($grantedUsernames))
            ->get();

        // 5. Ambil daftar tabel
        $tables = [];
        try {
            $rawTables = DB::connection('mysql_lab')->select("
            SELECT TABLE_NAME as table_name 
            FROM information_schema.TABLES 
            WHERE LOWER(TABLE_SCHEMA) = LOWER(?)
        ", [$dbName]);

            $tables = array_map(function ($row) {
                return current((array) $row);
            }, $rawTables);
        } catch (\Exception $e) {
            $tables = [];
        }

        return view('databases.show', compact('database', 'students', 'tables', 'grantedAccess'));
    }

    public function grantAccess(Request $request, $id)
    {
        $request->validate([
            'users'       => 'required|array|min:1',
            'permissions' => 'required|array|min:1',
            'all_tables'  => 'nullable|boolean',
            'tables'      => 'required_without:all_tables|array',
        ]);

        $database = Student::findOrFail($id);
        $dbName   = trim($database->mysql_database);
        $students = Student::whereIn('id', $request->users)->get();

        if (in_array('ALL', $request->permissions)) {
            $privileges = 'ALL PRIVILEGES';
        } else {
            $allowed = array_intersect($request->permissions, ['SELECT', 'INSERT', 'UPDATE', 'DELETE', 'EXECUTE']);
            $privileges = implode(', ', $allowed);
        }

        $tablesToGrant = $request->has('all_tables') ? ['*'] : $request->tables;

        try {
            foreach ($students as $student) {
                $username = trim($student->mysql_username);
                if (!$username) continue;

                $userHosts = DB::connection('mysql_lab')->select("SELECT Host FROM mysql.user WHERE User = ?", [$username]);
                $hosts = !empty($userHosts) ? collect($userHosts)->pluck('Host')->toArray() : ['%'];

                foreach ($hosts as $host) {
                    foreach ($tablesToGrant as $table) {
                        $target = ($table === '*') ? "`{$dbName}`.*" : "`{$dbName}`.`{$table}`";
                        $grantSql = "GRANT {$privileges} ON {$target} TO '{$username}'@'{$host}'";
                        DB::connection('mysql_lab')->statement($grantSql);
                    }
                }
            }

            DB::connection('mysql_lab')->statement("FLUSH PRIVILEGES;");
            return redirect()->back()->with('success', 'Hak akses berhasil diterapkan.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menerapkan hak akses: ' . $e->getMessage());
        }
    }

    public function revokeAccess(Request $request, $id)
    {
        $request->validate([
            'username' => 'required|string',
            'host'     => 'required|string',
            'table'    => 'required|string',
        ]);

        $database = Student::findOrFail($id);
        $dbName   = trim($database->mysql_database);
        $username = trim($request->username);
        $host     = trim($request->host);
        $table    = trim($request->table);

        try {
            $target = ($table === '*') ? "`{$dbName}`.*" : "`{$dbName}`.`{$table}`";
            $revokeSql = "REVOKE ALL PRIVILEGES ON {$target} FROM '{$username}'@'{$host}'";

            DB::connection('mysql_lab')->statement($revokeSql);
            DB::connection('mysql_lab')->statement("FLUSH PRIVILEGES;");

            return redirect()->back()->with('success', "Hak akses '{$username}' pada target '{$table}' berhasil dicabut.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mencabut hak akses: ' . $e->getMessage());
        }
    }
}
