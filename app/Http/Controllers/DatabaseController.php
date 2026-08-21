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

        // 1. Ambil daftar Mahasiswa (untuk pilihan form penerima akses)
        $students = Student::where('mysql_username', '!=', $database->mysql_username)
            ->whereNotNull('mysql_username')
            ->orderBy('nama', 'asc')
            ->get();

        // 2. Ambil daftar Tabel & View
        $tables    = [];
        $viewsList = [];
        try {
            $rawTables = DB::connection('mysql_lab')->select("
            SELECT TABLE_NAME as name, TABLE_TYPE as type 
            FROM information_schema.TABLES 
            WHERE LOWER(TABLE_SCHEMA) = LOWER(?)
              AND TABLE_TYPE IN ('BASE TABLE', 'VIEW')
            ORDER BY TABLE_NAME ASC
        ", [$dbName]);

            foreach ($rawTables as $row) {
                $isView = ($row->type === 'VIEW');
                $tables[] = (object) [
                    'name' => $row->name,
                    'type' => $isView ? 'VIEW' : 'TABLE'
                ];

                if ($isView) {
                    $viewsList[] = $row->name;
                }
            }
        } catch (\Exception $e) {
            $tables = [];
        }

        // 3. Ambil daftar Procedure & Function
        $procedures = [];
        $functions  = [];
        try {
            $routines = DB::connection('mysql_lab')->select("
            SELECT ROUTINE_NAME as name, ROUTINE_TYPE as type 
            FROM information_schema.ROUTINES 
            WHERE LOWER(ROUTINE_SCHEMA) = LOWER(?)
            ORDER BY ROUTINE_NAME ASC
        ", [$dbName]);

            foreach ($routines as $r) {
                if ($r->type === 'PROCEDURE') {
                    $procedures[] = (object) ['name' => $r->name];
                } else if ($r->type === 'FUNCTION') {
                    $functions[] = (object) ['name' => $r->name];
                }
            }
        } catch (\Exception $e) {
            $procedures = [];
            $functions  = [];
        }

        // 4. Ambil Hak Akses yang Sedang Aktif (Granted Access)
        $allStudents   = Student::whereNotNull('mysql_username')->get();
        $grantedAccess = [];

        foreach ($allStudents as $student) {
            $username = trim($student->mysql_username);
            if (!$username) continue;
            if (!$username || $username === trim($database->mysql_username)) {
                continue;
            }

            try {
                // Ambil daftar Host aktif milik user di mysql.user
                $userHosts = DB::connection('mysql_lab')->select("SELECT Host FROM mysql.user WHERE User = ?", [$username]);
                if (empty($userHosts)) continue;

                foreach ($userHosts as $h) {
                    $host = $h->Host;

                    // Jalankan perintah SHOW GRANTS
                    $grants = DB::connection('mysql_lab')->select("SHOW GRANTS FOR '{$username}'@'{$host}'");

                    foreach ($grants as $grantObj) {
                        $grantSql = array_values((array) $grantObj)[0];

                        // Regex Parser untuk membaca sintaks MySQL GRANT:
                        // Captures: 1 = Privileges, 2 = Routine Type (PROCEDURE/FUNCTION), 3 = Target Object (* or name)
                        $pattern = "/GRANT\s+(.*?)\s+ON\s+(?:(PROCEDURE|FUNCTION)\s+)?`?" . preg_quote($dbName, '/') . "`?\.\s*`?(\*|[a-zA-Z0-9_$-]+)`?/i";

                        if (preg_match($pattern, $grantSql, $matches)) {
                            $privileges  = $matches[1];
                            $routineType = strtoupper($matches[2] ?? '');
                            $targetObj   = $matches[3];

                            // Determinasi Tipe Objek
                            if ($targetObj === '*') {
                                $type = 'DATABASE';
                            } elseif ($routineType === 'PROCEDURE') {
                                $type = 'PROCEDURE';
                            } elseif ($routineType === 'FUNCTION') {
                                $type = 'FUNCTION';
                            } elseif (in_array($targetObj, $viewsList)) {
                                $type = 'VIEW';
                            } else {
                                $type = 'TABLE';
                            }

                            $grantedAccess[] = [
                                'nim'          => $student->nim,
                                'student_name' => $student->nama,
                                'username'     => $username,
                                'host'         => $host,
                                'type'         => $type,
                                'table'        => $targetObj,
                                'privileges'   => str_replace('GRANT ', '', $privileges)
                            ];
                        }
                    }
                }
            } catch (\Exception $e) {
                continue;
            }
        }

        // 5. Fitur Filter Pencarian & Sorting
        $collection = collect($grantedAccess);

        // Filter Kata Kunci
        if ($request->filled('search_active')) {
            $search = strtolower($request->search_active);
            $collection = $collection->filter(function ($item) use ($search) {
                return str_contains(strtolower($item['nim']), $search) ||
                    str_contains(strtolower($item['student_name']), $search) ||
                    str_contains(strtolower($item['username']), $search);
            });
        }

        // Filter Target Objek
        if ($request->filled('target_filter') && $request->target_filter !== 'all') {
            $filter = $request->target_filter;
            if (str_contains($filter, ':')) {
                [$fType, $fName] = explode(':', $filter, 2);
                $collection = $collection->where('type', $fType)->where('table', $fName);
            } elseif ($filter === '*') {
                $collection = $collection->where('table', '*');
            }
        }

        // Sorting
        $sortBy  = $request->get('sort_by', 'student_name');
        $sortDir = $request->get('sort_dir', 'asc') === 'desc';
        $grantedAccess = $collection->sortBy($sortBy, SORT_REGULAR, $sortDir)->values()->all();

        return view('databases.show', compact(
            'database',
            'students',
            'tables',
            'procedures',
            'functions',
            'grantedAccess'
        ));
    }

    public function grantAccess(Request $request, $id)
    {
        $request->validate([
            'users'          => 'required|array|min:1',
            'permissions'    => 'required|array|min:1',
            'all_tables'     => 'nullable|boolean',
            'tables'         => 'nullable|array',
            'all_procedures' => 'nullable|boolean',
            'procedures'     => 'nullable|array',
            'all_functions'  => 'nullable|boolean',
            'functions'      => 'nullable|array',
        ], [
            'users.required'       => 'Pilih minimal satu mahasiswa.',
            'permissions.required' => 'Pilih minimal satu hak akses (privilege).',
        ]);

        if (
            !$request->has('all_tables') && empty($request->tables) &&
            !$request->has('all_procedures') && empty($request->procedures) &&
            !$request->has('all_functions') && empty($request->functions)
        ) {
            return redirect()->back()->withErrors(['target' => 'Pilih minimal satu Tabel, View, Procedure, atau Function target.']);
        }

        $database = Student::findOrFail($id);
        $dbName   = trim($database->mysql_database);
        $students = Student::whereIn('id', $request->users)->get();

        $permissions      = $request->permissions;
        $isAll            = in_array('ALL', $permissions);
        $hasCreateRoutine = in_array('CREATE ROUTINE', $permissions);

        // 1. Privileges valid untuk Database Level (db.*)
        $schemaAllowed = [
            'SELECT',
            'INSERT',
            'UPDATE',
            'DELETE',
            'CREATE',
            'ALTER',
            'DROP',
            'INDEX',
            'REFERENCES',
            'TRIGGER',
            'CREATE VIEW',
            'SHOW VIEW',
            'CREATE ROUTINE',
            'ALTER ROUTINE',
            'EXECUTE'
        ];

        // 2. Privileges valid khusus Tabel Spesifik (db.table_name)
        $tableOnlyAllowed = [
            'SELECT',
            'INSERT',
            'UPDATE',
            'DELETE',
            'CREATE',
            'ALTER',
            'DROP',
            'INDEX',
            'REFERENCES',
            'TRIGGER'
        ];

        // 3. Privileges valid khusus Routine Spesifik (PROCEDURE / FUNCTION)
        $routineAllowed = ['EXECUTE', 'ALTER ROUTINE'];

        $schemaPrivileges  = $isAll ? 'ALL PRIVILEGES' : implode(', ', array_intersect($permissions, $schemaAllowed));
        $tablePrivileges   = $isAll ? 'ALL PRIVILEGES' : implode(', ', array_intersect($permissions, $tableOnlyAllowed));
        $routinePrivileges = $isAll ? 'ALL PRIVILEGES' : implode(', ', array_intersect($permissions, $routineAllowed));

        // Ambil daftar Procedure target
        $targetProcedures = [];
        if ($request->has('all_procedures')) {
            $rawProcs = DB::connection('mysql_lab')->select("
            SELECT ROUTINE_NAME FROM information_schema.ROUTINES 
            WHERE LOWER(ROUTINE_SCHEMA) = LOWER(?) AND ROUTINE_TYPE = 'PROCEDURE'
        ", [$dbName]);
            $targetProcedures = collect($rawProcs)->pluck('ROUTINE_NAME')->toArray();
        } elseif (!empty($request->procedures)) {
            $targetProcedures = $request->procedures;
        }

        // Ambil daftar Function target
        $targetFunctions = [];
        if ($request->has('all_functions')) {
            $rawFuncs = DB::connection('mysql_lab')->select("
            SELECT ROUTINE_NAME FROM information_schema.ROUTINES 
            WHERE LOWER(ROUTINE_SCHEMA) = LOWER(?) AND ROUTINE_TYPE = 'FUNCTION'
        ", [$dbName]);
            $targetFunctions = collect($rawFuncs)->pluck('ROUTINE_NAME')->toArray();
        } elseif (!empty($request->functions)) {
            $targetFunctions = $request->functions;
        }

        // Validasi pencegahan query kosong
        if ($request->has('all_tables') && empty($schemaPrivileges)) {
            return redirect()->back()->withErrors(['permissions' => 'Pilih hak akses yang valid untuk tingkat database.']);
        }
        if (!empty($request->tables) && empty($tablePrivileges) && !$hasCreateRoutine) {
            return redirect()->back()->withErrors(['permissions' => 'Pilih hak akses tabel yang valid (misal: SELECT, INSERT, UPDATE, DELETE).']);
        }
        if ((!empty($targetProcedures) || !empty($targetFunctions)) && empty($routinePrivileges) && !$hasCreateRoutine) {
            return redirect()->back()->withErrors(['permissions' => 'Hak akses Routine hanya mendukung EXECUTE, ALTER ROUTINE, atau ALL PRIVILEGES.']);
        }

        try {
            foreach ($students as $student) {
                $username = trim($student->mysql_username);
                if (!$username) continue;

                $userHosts = DB::connection('mysql_lab')->select("SELECT Host FROM mysql.user WHERE User = ?", [$username]);
                $hosts = !empty($userHosts) ? collect($userHosts)->pluck('Host')->toArray() : ['%'];

                foreach ($hosts as $host) {
                    // A. Grant Seluruh Database (db.*)
                    if ($request->has('all_tables') && $schemaPrivileges) {
                        DB::connection('mysql_lab')->statement("GRANT {$schemaPrivileges} ON `{$dbName}`.* TO '{$username}'@'{$host}'");
                    } else {
                        // LOGIK KHUSUS: Jika tidak memilih "Semua Tabel", tetapi mencentang "CREATE ROUTINE"
                        if ($hasCreateRoutine) {
                            DB::connection('mysql_lab')->statement("GRANT CREATE ROUTINE ON `{$dbName}`.* TO '{$username}'@'{$host}'");
                        }

                        // B. Grant Tabel Spesifik (db.table_name)
                        if (!empty($request->tables) && $tablePrivileges) {
                            foreach ($request->tables as $table) {
                                DB::connection('mysql_lab')->statement("GRANT {$tablePrivileges} ON `{$dbName}`.`{$table}` TO '{$username}'@'{$host}'");
                            }
                        }
                    }

                    // C. Grant Stored Procedures Spesifik
                    if (!empty($targetProcedures) && $routinePrivileges) {
                        foreach ($targetProcedures as $proc) {
                            DB::connection('mysql_lab')->statement("GRANT {$routinePrivileges} ON PROCEDURE `{$dbName}`.`{$proc}` TO '{$username}'@'{$host}'");
                        }
                    }

                    // D. Grant Stored Functions Spesifik
                    if (!empty($targetFunctions) && $routinePrivileges) {
                        foreach ($targetFunctions as $func) {
                            DB::connection('mysql_lab')->statement("GRANT {$routinePrivileges} ON FUNCTION `{$dbName}`.`{$func}` TO '{$username}'@'{$host}'");
                        }
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
            'type'     => 'nullable|string',
        ]);

        $database = Student::findOrFail($id);
        $dbName   = trim($database->mysql_database);

        $username = trim($request->username);
        $host     = trim($request->host);
        $target   = trim($request->table);
        $type     = strtoupper(trim($request->type ?? 'TABLE'));

        try {
            if ($target === '*' || $type === 'DATABASE') {
                // Cabut Akses Seluruh Database (db.*)
                DB::connection('mysql_lab')->statement("REVOKE ALL PRIVILEGES ON `{$dbName}`.* FROM '{$username}'@'{$host}'");
            } elseif ($type === 'PROCEDURE') {
                // Cabut Akses Stored Procedure
                DB::connection('mysql_lab')->statement("REVOKE ALL PRIVILEGES ON PROCEDURE `{$dbName}`.`{$target}` FROM '{$username}'@'{$host}'");
            } elseif ($type === 'FUNCTION') {
                // Cabut Akses Stored Function
                DB::connection('mysql_lab')->statement("REVOKE ALL PRIVILEGES ON FUNCTION `{$dbName}`.`{$target}` FROM '{$username}'@'{$host}'");
            } else {
                // Cabut Akses Table / View
                DB::connection('mysql_lab')->statement("REVOKE ALL PRIVILEGES ON `{$dbName}`.`{$target}` FROM '{$username}'@'{$host}'");
            }

            DB::connection('mysql_lab')->statement("FLUSH PRIVILEGES;");

            return redirect()->back()->with('success', 'Hak akses berhasil dicabut.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mencabut hak akses: ' . $e->getMessage());
        }
    }
}
