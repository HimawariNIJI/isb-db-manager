@extends('layouts.app')

@section('title', 'Detail Database - ISB DB Manager')

@section('content')

    <div class="dashboard-wrapper">

        <!-- Sidebar -->
        <aside class="sidebar">

            <div class="sidebar-brand">
                <div class="brand-logo small">ISB</div>
                <div>
                    <h5>ISB DB Manager</h5>
                    <span>Database Management</span>
                </div>
            </div>

            <nav class="sidebar-nav">
                <a href="{{ route('dashboard') }}" class="nav-item"><span>▦</span> Dashboard</a>
                <a href="{{ route('students.create') }}" class="nav-item"><span>＋</span> Tambah Mahasiswa</a>
                <a href="{{ route('students.import') }}" class="nav-item"><span>⇧</span> Import CSV</a>
                <a href="{{ route('students.index') }}" class="nav-item"><span>☷</span> Daftar Mahasiswa</a>
                <a href="{{ route('databases.index') }}" class="nav-item active"><span>🗄</span> Manage Database</a>
            </nav>

            <div class="sidebar-bottom">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="nav-item logout border-0 bg-transparent w-100 text-start">
                        <span>↪</span> Logout
                    </button>
                </form>
            </div>

        </aside>

        <!-- Main Content -->
        <main class="main-content">

            <!-- Header -->
            <header class="top-header">
                <div>
                    <h4>{{ $database->mysql_database ?? 'db_sample' }}</h4>
                    <p>Atur hak akses pengguna dan izin tabel, procedure, serta function secara spesifik</p>
                </div>

                <div class="admin-profile">
                    <div class="admin-avatar">A</div>
                    <div>
                        <strong>Administrator</strong>
                        <small>Dosen</small>
                    </div>
                </div>
            </header>

            <!-- Content Container -->
            <div class="content-container">

                <div class="mb-3">
                    <a href="{{ route('databases.index') }}" class="btn btn-sm btn-outline-secondary">
                        ← Kembali ke Daftar Database
                    </a>
                </div>

                <!-- Alert Messages -->
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                        <strong>Berhasil!</strong> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                        <strong>Gagal!</strong> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-warning alert-dismissible fade show mb-4" role="alert">
                        <strong>Periksa kembali inputan Anda:</strong>
                        <ul class="mb-0 mt-1 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <form action="{{ route('databases.grant', $database->id) }}" method="POST">
                    @csrf

                    <div class="row g-4">

                        <!-- Kolom Kiri: Target Selection -->
                        <div class="col-lg-6">

                            <!-- 1. Pilih User / Mahasiswa -->
                            <div class="section-card mb-4">
                                <div class="section-header">
                                    <div>
                                        <h5>1. Pilih User / Mahasiswa</h5>
                                        <p>Centang user yang akan diberikan hak akses</p>
                                    </div>
                                </div>

                                <div class="table-responsive">
                                    <table class="table custom-table align-middle">
                                        <thead>
                                            <tr>
                                                <th style="width: 40px;">
                                                    <input class="form-check-input" type="checkbox" id="selectAllUsers">
                                                </th>
                                                <th>NIM</th>
                                                <th>Nama Mahasiswa</th>
                                                <th>Username DB</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($students as $student)
                                                <tr>
                                                    <td>
                                                        <input class="form-check-input user-checkbox" type="checkbox"
                                                            name="users[]" value="{{ $student->id }}">
                                                    </td>
                                                    <td>{{ $student->nim }}</td>
                                                    <td>{{ $student->nama }}</td>
                                                    <td><code>{{ $student->mysql_username }}</code></td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="text-center py-3 text-muted">
                                                        Belum ada data mahasiswa terdaftar.
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- 2. Pilih Tabel / View Target -->
                            <div class="section-card mb-4">
                                <div class="section-header border-bottom pb-2 mb-3">
                                    <div class="d-flex align-items-center justify-content-between w-100">
                                        <div>
                                            <h5>2. Pilih Tabel / View Target</h5>
                                            <p class="mb-0">Pilih tabel atau view yang boleh diakses</p>
                                        </div>
                                        @if (count($tables) == 0)
                                            <span class="badge bg-secondary" style="font-size: 0.65rem;">Tidak
                                                Tersedia</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="p-3 pt-0">
                                    <div class="form-check mb-3 pb-2 border-bottom">
                                        <input class="form-check-input" type="checkbox" id="allTables" name="all_tables"
                                            value="1" {{ count($tables) == 0 ? 'disabled' : '' }}>
                                        <label
                                            class="form-check-label fw-bold {{ count($tables) == 0 ? 'text-muted' : '' }}"
                                            for="allTables">
                                            Semua Tabel & View (*)
                                        </label>
                                        <div class="form-text">Memberikan akses ke seluruh tabel dan view dalam database
                                            ini.</div>
                                    </div>

                                    <div class="row g-2">
                                        @forelse($tables as $item)
                                            <div class="col-md-6">
                                                <div class="form-check d-flex align-items-center gap-2">
                                                    <input class="form-check-input table-checkbox" type="checkbox"
                                                        name="tables[]" value="{{ $item->name }}"
                                                        id="tbl_{{ $item->name }}">
                                                    <label class="form-check-label d-flex align-items-center gap-1"
                                                        for="tbl_{{ $item->name }}">
                                                        <code>{{ $item->name }}</code>
                                                        @if ($item->type === 'VIEW')
                                                            <span class="badge bg-info text-dark"
                                                                style="font-size: 0.65rem;">VIEW</span>
                                                        @else
                                                            <span class="badge bg-light text-secondary border"
                                                                style="font-size: 0.65rem;">TABLE</span>
                                                        @endif
                                                    </label>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="col-12 py-2 text-muted">
                                                <small>⚠️ Belum ada tabel atau view yang dibuat.</small>
                                            </div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>

                            <!-- 3. Pilih Stored Procedure Target -->
                            <div class="section-card mb-4">
                                <div class="section-header border-bottom pb-2 mb-3">
                                    <div class="d-flex align-items-center justify-content-between w-100">
                                        <div>
                                            <h5>3. Pilih Stored Procedure Target</h5>
                                            <p class="mb-0">Pilih Stored Procedure yang boleh dikelola / dieksekusi</p>
                                        </div>
                                        @if (count($procedures) == 0)
                                            <span class="badge bg-secondary" style="font-size: 0.65rem;">Tidak
                                                Tersedia</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="p-3 pt-0">
                                    <div class="form-check mb-3 pb-2 border-bottom">
                                        <input class="form-check-input" type="checkbox" id="allProcedures"
                                            name="all_procedures" value="1"
                                            {{ count($procedures) == 0 ? 'disabled' : '' }}>
                                        <label
                                            class="form-check-label fw-bold {{ count($procedures) == 0 ? 'text-muted' : '' }}"
                                            for="allProcedures">
                                            Semua Stored Procedure (*)
                                        </label>
                                        <div class="form-text">Memberikan akses ke seluruh Stored Procedure dalam database
                                            ini.</div>
                                    </div>

                                    <div class="row g-2">
                                        @forelse($procedures as $proc)
                                            <div class="col-md-6">
                                                <div class="form-check d-flex align-items-center gap-2">
                                                    <input class="form-check-input routine-checkbox procedure-checkbox"
                                                        type="checkbox" name="procedures[]" value="{{ $proc->name }}"
                                                        id="proc_{{ $proc->name }}">
                                                    <label class="form-check-label d-flex align-items-center gap-1"
                                                        for="proc_{{ $proc->name }}">
                                                        <code>{{ $proc->name }}()</code>
                                                        <span class="badge bg-warning text-dark"
                                                            style="font-size: 0.65rem;">PROCEDURE</span>
                                                    </label>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="col-12 py-2 text-muted">
                                                <small>⚠️ Belum ada Stored Procedure yang dibuat.</small>
                                            </div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>

                            <!-- 4. Pilih Stored Function Target -->
                            <div class="section-card mb-4">
                                <div class="section-header border-bottom pb-2 mb-3">
                                    <div class="d-flex align-items-center justify-content-between w-100">
                                        <div>
                                            <h5>4. Pilih Stored Function Target</h5>
                                            <p class="mb-0">Pilih Stored Function yang boleh dikelola / dieksekusi</p>
                                        </div>
                                        @if (count($functions) == 0)
                                            <span class="badge bg-secondary" style="font-size: 0.65rem;">Tidak
                                                Tersedia</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="p-3 pt-0">
                                    <div class="form-check mb-3 pb-2 border-bottom">
                                        <input class="form-check-input" type="checkbox" id="allFunctions"
                                            name="all_functions" value="1"
                                            {{ count($functions) == 0 ? 'disabled' : '' }}>
                                        <label
                                            class="form-check-label fw-bold {{ count($functions) == 0 ? 'text-muted' : '' }}"
                                            for="allFunctions">
                                            Semua Stored Function (*)
                                        </label>
                                        <div class="form-text">Memberikan akses ke seluruh Stored Function dalam database
                                            ini.</div>
                                    </div>

                                    <div class="row g-2">
                                        @forelse($functions as $func)
                                            <div class="col-md-6">
                                                <div class="form-check d-flex align-items-center gap-2">
                                                    <input class="form-check-input routine-checkbox function-checkbox"
                                                        type="checkbox" name="functions[]" value="{{ $func->name }}"
                                                        id="func_{{ $func->name }}">
                                                    <label class="form-check-label d-flex align-items-center gap-1"
                                                        for="func_{{ $func->name }}">
                                                        <code>{{ $func->name }}()</code>
                                                        <span class="badge bg-dark text-white"
                                                            style="font-size: 0.65rem;">FUNCTION</span>
                                                    </label>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="col-12 py-2 text-muted">
                                                <small>⚠️ Belum ada Stored Function yang dibuat.</small>
                                            </div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>

                        </div>

                        <!-- Kolom Kanan: Hak Akses (Privileges Terkelompok) & Submit -->
                        <div class="col-lg-6">

                            <div class="section-card position-sticky" style="top: 20px;">
                                <div class="section-header border-bottom pb-3 mb-3">
                                    <div>
                                        <h5>Hak Akses (Privileges)</h5>
                                        <p class="mb-0">Pilih jenis operasi yang diizinkan</p>
                                    </div>
                                </div>

                                <div class="p-3 pt-0">

                                    <div class="row g-3 mb-4">
                                        <!-- 1. DATA -->
                                        <div class="col-md-6">
                                            <div class="border rounded p-3 bg-light h-100">
                                                <div
                                                    class="d-flex justify-content-between align-items-center mb-2 pb-1 border-bottom">
                                                    <strong class="text-uppercase text-secondary small">DATA</strong>
                                                    <div class="form-check mb-0">
                                                        <input class="form-check-input select-all-group" type="checkbox"
                                                            id="selectAllData" data-target=".data-perm">
                                                        <label class="form-check-label small text-muted fw-semibold"
                                                            for="selectAllData">Pilih Semua</label>
                                                    </div>
                                                </div>
                                                <div class="d-flex flex-column gap-1">
                                                    <div class="form-check">
                                                        <input
                                                            class="form-check-input perm-checkbox table-perm-checkbox data-perm"
                                                            type="checkbox" name="permissions[]" value="SELECT"
                                                            id="permSelect">
                                                        <label class="form-check-label" for="permSelect">SELECT</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input
                                                            class="form-check-input perm-checkbox table-perm-checkbox data-perm"
                                                            type="checkbox" name="permissions[]" value="INSERT"
                                                            id="permInsert">
                                                        <label class="form-check-label" for="permInsert">INSERT</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input
                                                            class="form-check-input perm-checkbox table-perm-checkbox data-perm"
                                                            type="checkbox" name="permissions[]" value="UPDATE"
                                                            id="permUpdate">
                                                        <label class="form-check-label" for="permUpdate">UPDATE</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input
                                                            class="form-check-input perm-checkbox table-perm-checkbox data-perm"
                                                            type="checkbox" name="permissions[]" value="DELETE"
                                                            id="permDelete">
                                                        <label class="form-check-label" for="permDelete">DELETE</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- 2. TABLE -->
                                        <div class="col-md-6">
                                            <div class="border rounded p-3 bg-light h-100">
                                                <div
                                                    class="d-flex justify-content-between align-items-center mb-2 pb-1 border-bottom">
                                                    <strong class="text-uppercase text-secondary small">TABLE</strong>
                                                    <div class="form-check mb-0">
                                                        <input class="form-check-input select-all-group" type="checkbox"
                                                            id="selectAllTable" data-target=".table-structure-perm">
                                                        <label class="form-check-label small text-muted fw-semibold"
                                                            for="selectAllTable">Pilih Semua</label>
                                                    </div>
                                                </div>
                                                <div class="d-flex flex-column gap-1">
                                                    <div class="form-check">
                                                        <input
                                                            class="form-check-input perm-checkbox table-perm-checkbox table-structure-perm"
                                                            type="checkbox" name="permissions[]" value="CREATE"
                                                            id="permCreate">
                                                        <label class="form-check-label" for="permCreate">CREATE</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input
                                                            class="form-check-input perm-checkbox table-perm-checkbox table-structure-perm"
                                                            type="checkbox" name="permissions[]" value="ALTER"
                                                            id="permAlter">
                                                        <label class="form-check-label" for="permAlter">ALTER</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input
                                                            class="form-check-input perm-checkbox table-perm-checkbox table-structure-perm"
                                                            type="checkbox" name="permissions[]" value="DROP"
                                                            id="permDrop">
                                                        <label class="form-check-label" for="permDrop">DROP</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input
                                                            class="form-check-input perm-checkbox table-perm-checkbox table-structure-perm"
                                                            type="checkbox" name="permissions[]" value="INDEX"
                                                            id="permIndex">
                                                        <label class="form-check-label" for="permIndex">INDEX</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input
                                                            class="form-check-input perm-checkbox table-perm-checkbox table-structure-perm"
                                                            type="checkbox" name="permissions[]" value="REFERENCES"
                                                            id="permReferences">
                                                        <label class="form-check-label"
                                                            for="permReferences">REFERENCES</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input
                                                            class="form-check-input perm-checkbox table-perm-checkbox table-structure-perm"
                                                            type="checkbox" name="permissions[]" value="TRIGGER"
                                                            id="permTrigger">
                                                        <label class="form-check-label" for="permTrigger">TRIGGER</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- 3. VIEW -->
                                        <div class="col-md-6">
                                            <div class="border rounded p-3 bg-light h-100">
                                                <div
                                                    class="d-flex justify-content-between align-items-center mb-2 pb-1 border-bottom">
                                                    <strong class="text-uppercase text-secondary small">VIEW</strong>
                                                    <div class="form-check mb-0">
                                                        <input class="form-check-input select-all-group" type="checkbox"
                                                            id="selectAllView" data-target=".view-perm">
                                                        <label class="form-check-label small text-muted fw-semibold"
                                                            for="selectAllView">Pilih Semua</label>
                                                    </div>
                                                </div>
                                                <div class="d-flex flex-column gap-1">
                                                    <div class="form-check">
                                                        <input
                                                            class="form-check-input perm-checkbox table-perm-checkbox view-perm"
                                                            type="checkbox" name="permissions[]" value="CREATE VIEW"
                                                            id="permCreateView">
                                                        <label class="form-check-label" for="permCreateView">CREATE
                                                            VIEW</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input
                                                            class="form-check-input perm-checkbox table-perm-checkbox view-perm"
                                                            type="checkbox" name="permissions[]" value="SHOW VIEW"
                                                            id="permShowView">
                                                        <label class="form-check-label" for="permShowView">SHOW
                                                            VIEW</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- 4. PROCEDURE / FUNCTION -->
                                        <div class="col-md-6">
                                            <div class="border rounded p-3 bg-light h-100">
                                                <div
                                                    class="d-flex justify-content-between align-items-center mb-2 pb-1 border-bottom">
                                                    <strong class="text-uppercase text-secondary small">PROCEDURE /
                                                        FUNCTION</strong>
                                                    <div class="form-check mb-0">
                                                        <input class="form-check-input select-all-group" type="checkbox"
                                                            id="selectAllRoutine" data-target=".routine-perm">
                                                        <label class="form-check-label small text-muted fw-semibold"
                                                            for="selectAllRoutine">Pilih Semua</label>
                                                    </div>
                                                </div>
                                                <div class="d-flex flex-column gap-1">
                                                    <div class="form-check">
                                                        <input
                                                            class="form-check-input perm-checkbox routine-perm-checkbox routine-perm"
                                                            type="checkbox" name="permissions[]" value="CREATE ROUTINE"
                                                            id="permCreateRoutine">
                                                        <label class="form-check-label" for="permCreateRoutine">CREATE
                                                            ROUTINE (Global)</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input
                                                            class="form-check-input perm-checkbox routine-perm-checkbox routine-perm"
                                                            type="checkbox" name="permissions[]" value="ALTER ROUTINE"
                                                            id="permAlterRoutine">
                                                        <label class="form-check-label" for="permAlterRoutine">ALTER
                                                            ROUTINE</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input
                                                            class="form-check-input perm-checkbox routine-perm-checkbox routine-perm"
                                                            type="checkbox" name="permissions[]" value="EXECUTE"
                                                            id="permExecute">
                                                        <label class="form-check-label" for="permExecute">EXECUTE</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <hr>

                                    <button type="submit" class="btn btn-primary custom-btn w-100 py-2">
                                        Simpan & Terapkan Hak Akses
                                    </button>
                                </div>
                            </div>

                        </div>

                    </div>

                </form>

                <!-- 5. Tabel Mahasiswa yang Sudah Memiliki Akses -->
                <div class="section-card mt-4">
                    <div class="section-header border-bottom pb-3 mb-3">
                        <div class="d-flex flex-wrap align-items-center justify-content-between w-100 gap-2">
                            <div>
                                <h5 class="mb-1">Hak Akses yang Sedang Aktif</h5>
                                <p class="text-muted small mb-0">Daftar mahasiswa beserta rincian target objek (Table,
                                    View, Procedure, Function) dan hak aksesnya</p>
                            </div>
                        </div>
                    </div>

                    <!-- Filter Bar -->
                    <div class="p-3 bg-light border-bottom">
                        <form action="{{ route('databases.show', $database->id) }}" method="GET"
                            class="row g-2 align-items-center">
                            <div class="col-md-4">
                                <input type="text" name="search_active" class="form-control form-control-sm"
                                    placeholder="🔍 Cari NIM, Nama, Username..." value="{{ request('search_active') }}">
                            </div>

                            <div class="col-md-3">
                                <select name="target_filter" class="form-select form-select-sm"
                                    onchange="this.form.submit()">
                                    <option value="all">-- Semua Target Objek --</option>
                                    <option value="*" {{ request('target_filter') === '*' ? 'selected' : '' }}>Akses
                                        Global Database (*)</option>

                                    @if (count($tables) > 0)
                                        <optgroup label="Tabel & View">
                                            @foreach ($tables as $item)
                                                <option value="TABLE:{{ $item->name }}"
                                                    {{ request('target_filter') === 'TABLE:' . $item->name ? 'selected' : '' }}>
                                                    [{{ $item->type }}] {{ $item->name }}
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    @endif

                                    @if (count($procedures) > 0)
                                        <optgroup label="Stored Procedures">
                                            @foreach ($procedures as $proc)
                                                <option value="PROCEDURE:{{ $proc->name }}"
                                                    {{ request('target_filter') === 'PROCEDURE:' . $proc->name ? 'selected' : '' }}>
                                                    [PROCEDURE] {{ $proc->name }}()
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    @endif

                                    @if (count($functions) > 0)
                                        <optgroup label="Stored Functions">
                                            @foreach ($functions as $func)
                                                <option value="FUNCTION:{{ $func->name }}"
                                                    {{ request('target_filter') === 'FUNCTION:' . $func->name ? 'selected' : '' }}>
                                                    [FUNCTION] {{ $func->name }}()
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    @endif
                                </select>
                            </div>

                            <div class="col-md-3">
                                <select name="sort_by" class="form-select form-select-sm" onchange="this.form.submit()">
                                    <option value="student_name"
                                        {{ request('sort_by', 'student_name') === 'student_name' ? 'selected' : '' }}>
                                        Urutkan: Nama</option>
                                    <option value="nim" {{ request('sort_by') === 'nim' ? 'selected' : '' }}>Urutkan:
                                        NIM</option>
                                    <option value="username" {{ request('sort_by') === 'username' ? 'selected' : '' }}>
                                        Urutkan: Username</option>
                                    <option value="table" {{ request('sort_by') === 'table' ? 'selected' : '' }}>Urutkan:
                                        Target Objek</option>
                                </select>
                            </div>

                            <div class="col-md-2 d-flex gap-1">
                                <select name="sort_dir" class="form-select form-select-sm" onchange="this.form.submit()">
                                    <option value="asc" {{ request('sort_dir', 'asc') === 'asc' ? 'selected' : '' }}>
                                        ASC ↑</option>
                                    <option value="desc" {{ request('sort_dir') === 'desc' ? 'selected' : '' }}>DESC ↓
                                    </option>
                                </select>

                                @if (request()->hasAny(['search_active', 'target_filter', 'sort_by', 'sort_dir']))
                                    <a href="{{ route('databases.show', $database->id) }}"
                                        class="btn btn-sm btn-outline-secondary" title="Reset Filter">✕</a>
                                @endif
                            </div>
                        </form>
                    </div>

                    <!-- Data Table -->
                    <div class="table-responsive">
                        <table class="table custom-table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>NIM</th>
                                    <th>Nama Mahasiswa</th>
                                    <th>Username DB</th>
                                    <th>Tipe Objek</th>
                                    <th>Nama Objek Target</th>
                                    <th>Hak Akses (Privileges)</th>
                                    <th class="text-center" style="width: 120px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($grantedAccess ?? [] as $access)
                                    @php
                                        $type = strtoupper($access['type'] ?? 'TABLE');
                                        if ($access['table'] === '*') {
                                            $type = 'DATABASE';
                                        }
                                    @endphp
                                    <tr>
                                        <td>{{ $access['nim'] }}</td>
                                        <td><strong>{{ $access['student_name'] }}</strong></td>
                                        <td><code>{{ $access['username'] }}</code></td>
                                        <td>
                                            @if ($type === 'DATABASE')
                                                <span class="badge bg-primary">DATABASE</span>
                                            @elseif ($type === 'VIEW')
                                                <span class="badge bg-info text-dark">VIEW</span>
                                            @elseif ($type === 'PROCEDURE')
                                                <span class="badge bg-warning text-dark">PROCEDURE</span>
                                            @elseif ($type === 'FUNCTION')
                                                <span class="badge bg-dark text-white">FUNCTION</span>
                                            @else
                                                <span class="badge bg-secondary">TABLE</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($access['table'] === '*')
                                                <span class="fw-bold text-primary">Semua Tabel & Routine (*)</span>
                                            @elseif ($type === 'PROCEDURE' || $type === 'FUNCTION')
                                                <code>{{ $access['table'] }}()</code>
                                            @else
                                                <code>{{ $access['table'] }}</code>
                                            @endif
                                        </td>
                                        <td style="max-width: 250px;" class="align-middle">
                                            <div class="d-flex flex-wrap gap-1">
                                                @php
                                                    // Pecah string hak akses jika dipisahkan oleh koma
                                                    $privList = is_array($access['privileges'])
                                                        ? $access['privileges']
                                                        : explode(', ', $access['privileges']);
                                                @endphp

                                                @foreach ($privList as $priv)
                                                    <span class="badge bg-light text-dark border font-monospace px-1 py-1"
                                                        style="font-size: 0.7rem;">
                                                        {{ $priv }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <form action="{{ route('databases.revoke', $database->id) }}" method="POST"
                                                onsubmit="return confirm('Apakah Anda yakin ingin mencabut akses ini?')">
                                                @csrf
                                                <input type="hidden" name="username" value="{{ $access['username'] }}">
                                                <input type="hidden" name="host" value="{{ $access['host'] }}">
                                                <input type="hidden" name="table" value="{{ $access['table'] }}">
                                                <input type="hidden" name="type" value="{{ $type }}">
                                                <button type="submit" class="btn btn-sm btn-outline-danger py-1 px-2">
                                                    Cabut
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">
                                            Data tidak ditemukan atau belum ada hak akses yang aktif.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // User Selectors
            const selectAllUsers = document.getElementById('selectAllUsers');
            const userCheckboxes = document.querySelectorAll('.user-checkbox');

            // Table Selectors
            const allTablesCb = document.getElementById('allTables');
            const tableCheckboxes = document.querySelectorAll('.table-checkbox');
            const tablePermCheckboxes = document.querySelectorAll('.table-perm-checkbox');
            const totalTables = {{ count($tables) }};

            // Procedure Selectors
            const allProceduresCb = document.getElementById('allProcedures');
            const procedureCheckboxes = document.querySelectorAll('.procedure-checkbox');

            // Function Selectors
            const allFunctionsCb = document.getElementById('allFunctions');
            const functionCheckboxes = document.querySelectorAll('.function-checkbox');

            // Combined Routine Selectors
            const routineCheckboxes = document.querySelectorAll('.routine-checkbox');
            const routinePermCheckboxes = document.querySelectorAll('.routine-perm-checkbox');
            const totalRoutines = {{ count($procedures) + count($functions) }};

            // Privilege Selectors
            const grantAllCb = document.getElementById('grantAll');
            const permCheckboxes = document.querySelectorAll('.perm-checkbox');

            // Notice & Create Routine Selectors
            const createRoutineCb = document.querySelector('input[value="CREATE ROUTINE"]');
            const noticeBox = document.getElementById('createRoutineNotice');

            // Group Select All Selectors
            const groupSelectAllCbs = document.querySelectorAll('.select-all-group');

            // Flag untuk mencegah konflik loop saat centang bersamaan
            let isBulkUpdating = false;

            // 1. Toggle Check All Users
            if (selectAllUsers) {
                selectAllUsers.addEventListener('change', function() {
                    userCheckboxes.forEach(cb => cb.checked = this.checked);
                });
            }

            // 2. Toggle All Tables Target
            if (allTablesCb) {
                allTablesCb.addEventListener('change', function() {
                    tableCheckboxes.forEach(cb => {
                        cb.disabled = this.checked;
                        if (this.checked) cb.checked = false;
                    });
                    updatePermissionsState();
                });
            }

            // 3. Toggle All Procedures Target
            if (allProceduresCb) {
                allProceduresCb.addEventListener('change', function() {
                    procedureCheckboxes.forEach(cb => {
                        cb.disabled = this.checked;
                        if (this.checked) cb.checked = false;
                    });
                    updatePermissionsState();
                });
            }

            // 4. Toggle All Functions Target
            if (allFunctionsCb) {
                allFunctionsCb.addEventListener('change', function() {
                    functionCheckboxes.forEach(cb => {
                        cb.disabled = this.checked;
                        if (this.checked) cb.checked = false;
                    });
                    updatePermissionsState();
                });
            }

            // 5. Toggle ALL PRIVILEGES
            if (grantAllCb) {
                grantAllCb.addEventListener('change', function() {
                    permCheckboxes.forEach(cb => {
                        cb.disabled = this.checked;
                        if (this.checked) cb.checked = false;
                    });
                    groupSelectAllCbs.forEach(cb => {
                        cb.disabled = this.checked;
                        if (this.checked) cb.checked = false;
                    });
                    if (!this.checked) {
                        updatePermissionsState();
                    }
                });
            }

            // 6. Logika Penonaktifan Otomatis Checkbox Privilege
            function updatePermissionsState() {
                if (grantAllCb && grantAllCb.checked) return;

                // --- TABEL / VIEW PERMISSIONS ---
                const isAllTablesSelected = allTablesCb && allTablesCb.checked;
                const isAnyTableSelected = Array.from(tableCheckboxes).some(cb => cb.checked);

                if (totalTables > 0 && (isAnyTableSelected || isAllTablesSelected)) {
                    tablePermCheckboxes.forEach(cb => cb.disabled = false);
                } else {
                    tablePermCheckboxes.forEach(cb => {
                        cb.disabled = true;
                        cb.checked = false;
                    });
                }

                // --- PROCEDURE / FUNCTION PERMISSIONS ---
                const isAllProceduresSelected = allProceduresCb && allProceduresCb.checked;
                const isAllFunctionsSelected = allFunctionsCb && allFunctionsCb.checked;
                const isAnyRoutineSelected = Array.from(routineCheckboxes).some(cb => cb.checked);

                if (totalRoutines > 0 && (isAnyRoutineSelected || isAllProceduresSelected ||
                        isAllFunctionsSelected)) {
                    routinePermCheckboxes.forEach(cb => cb.disabled = false);
                } else {
                    routinePermCheckboxes.forEach(cb => {
                        cb.disabled = true;
                        cb.checked = false;
                    });
                }

                syncGroupSelectAllStates();
            }

            // 7. Notifikasi Otomatis untuk CREATE ROUTINE
            if (createRoutineCb) {
                createRoutineCb.addEventListener('change', function() {
                    if (this.checked) {
                        if (noticeBox) {
                            noticeBox.classList.remove('d-none');
                            noticeBox.classList.add('d-flex');
                        }
                        if (allTablesCb && !allTablesCb.checked) {
                            allTablesCb.checked = true;
                            allTablesCb.dispatchEvent(new Event('change'));
                        }
                    } else {
                        if (noticeBox) {
                            noticeBox.classList.add('d-none');
                            noticeBox.classList.remove('d-flex');
                        }
                    }
                });
            }

            // 8. Logika 'Pilih Semua' per Kategori Group (FIXED)
            groupSelectAllCbs.forEach(groupCb => {
                groupCb.addEventListener('change', function() {
                    const targetClass = this.getAttribute('data-target');
                    const childCheckboxes = document.querySelectorAll(targetClass);
                    const isChecked = this.checked; // Kunci nilai awal sebelum loop

                    isBulkUpdating = true; // Kunci status sinkronisasi

                    childCheckboxes.forEach(cb => {
                        if (!cb.disabled) {
                            cb.checked = isChecked;
                        }
                    });

                    // Jika grup Routine, pemicu notifikasi CREATE ROUTINE
                    if (createRoutineCb && Array.from(childCheckboxes).includes(createRoutineCb)) {
                        createRoutineCb.dispatchEvent(new Event('change'));
                    }

                    isBulkUpdating = false; // Buka kunci status
                    syncGroupSelectAllStates
                        (); // Update centang header setelah seluruh anak tercentang
                });
            });

            // Helper untuk sinkronisasi status centang 'Pilih Semua'
            function syncGroupSelectAllStates() {
                if (isBulkUpdating) return; // Mencegah reset nilai di tengah proses loop

                ['.data-perm', '.table-structure-perm', '.view-perm', '.routine-perm'].forEach(groupSelector => {
                    const children = document.querySelectorAll(groupSelector);
                    const parentGroupCb = document.querySelector(
                        `.select-all-group[data-target="${groupSelector}"]`);
                    if (parentGroupCb) {
                        const enabledChildren = Array.from(children).filter(c => !c.disabled);
                        const allChecked = enabledChildren.length > 0 && enabledChildren.every(c => c
                            .checked);
                        const allDisabled = children.length > 0 && Array.from(children).every(c => c
                            .disabled);

                        parentGroupCb.disabled = allDisabled;
                        parentGroupCb.checked = allChecked;
                    }
                });
            }

            // Event Listeners
            tableCheckboxes.forEach(cb => cb.addEventListener('change', updatePermissionsState));
            routineCheckboxes.forEach(cb => cb.addEventListener('change', updatePermissionsState));
            permCheckboxes.forEach(cb => cb.addEventListener('change', syncGroupSelectAllStates));

            // Initial load check
            updatePermissionsState();
        });
    </script>
@endsection
