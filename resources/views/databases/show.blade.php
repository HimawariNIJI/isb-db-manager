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
                            <div class="card border-0 shadow-sm rounded-3 mb-4">
                                <div class="card-body p-4">
                                    <h5 class="fw-bold mb-1">Pilih User / Mahasiswa</h5>
                                    <p class="text-muted small mb-3">Centang user yang akan diberikan hak akses</p>

                                    <!-- Search Bar Filter -->
                                    <div class="mb-3">
                                        <input type="text" id="searchUserInput" class="form-control form-control-sm"
                                            placeholder="Cari NIM, Nama, atau Username DB...">
                                    </div>

                                    <!-- Scrollable Container (Tinggi maks untuk 5 baris + header sticky) -->
                                    <div class="table-responsive border rounded"
                                        style="max-height: 270px; overflow-y: auto;">
                                        <table class="table table-hover align-middle mb-0" style="width: 100%;">
                                            <thead class="table-light small text-uppercase"
                                                style="position: sticky; top: 0; background-color: #f8f9fa; z-index: 2;">
                                                <tr>
                                                    <th style="width: 10%;" class="text-center">
                                                        <input class="form-check-input" type="checkbox" id="selectAllUsers">
                                                    </th>
                                                    <th style="width: 30%;">NIM</th>
                                                    <th style="width: 35%;">NAMA MAHASISWA</th>
                                                    <th style="width: 25%;">USERNAME DB</th>
                                                </tr>
                                            </thead>
                                            <tbody class="small">
                                                @foreach ($students as $student)
                                                    <tr class="user-row">
                                                        <td class="text-center">
                                                            <!-- ATRIBUT name="users[]" DAN value PENTING UNTUK CONTROLLER -->
                                                            <input class="form-check-input user-checkbox" type="checkbox"
                                                                name="users[]" value="{{ $student->id }}">
                                                        </td>
                                                        <td class="search-nim">{{ $student->nim }}</td>
                                                        <td class="fw-bold search-nama">{{ $student->nama }}</td>
                                                        <td class="text-danger font-monospace search-user">
                                                            {{ $student->mysql_username }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- 2. Pilih Tabel / View Target -->
                            <div class="section-card mb-4">
                                <div class="section-header border-bottom pb-2 mb-3">
                                    <div class="d-flex align-items-center justify-content-between w-100">
                                        <div>
                                            <h5>Pilih Tabel / View Target</h5>
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
                                            <h5>Pilih Stored Procedure Target</h5>
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
                                            <h5>Pilih Stored Function Target</h5>
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
                                        <!-- 1. DATABASE -->
                                        <div class="col-md-6">
                                            <div class="border rounded p-3 bg-light h-100">
                                                <div
                                                    class="d-flex justify-content-between align-items-center mb-2 pb-1 border-bottom">
                                                    <strong class="text-uppercase text-secondary small">DATABASE</strong>
                                                    <div class="form-check mb-0">
                                                        <input class="form-check-input select-all-group" type="checkbox"
                                                            id="selectAllDatabase" data-target=".database-perm">
                                                        <label class="form-check-label small text-muted fw-semibold"
                                                            for="selectAllDatabase">Pilih Semua</label>
                                                    </div>
                                                </div>
                                                <div class="d-flex flex-column gap-1">
                                                    <div class="form-check">
                                                        <input
                                                            class="form-check-input perm-checkbox database-perm"
                                                            type="checkbox" name="permissions[]" value="CREATE"
                                                            id="permCreate">
                                                        <label class="form-check-label" for="permCreate">CREATE</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input
                                                            class="form-check-input perm-checkbox database-perm"
                                                            type="checkbox" name="permissions[]" value="CREATE VIEW"
                                                            id="permCreateView">
                                                        <label class="form-check-label" for="permCreateView">CREATE VIEW</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input
                                                            class="form-check-input perm-checkbox database-perm"
                                                            type="checkbox" name="permissions[]" value="CREATE ROUTINE"
                                                            id="permCreateRoutine">
                                                        <label class="form-check-label" for="permCreateRoutine">CREATE ROUTINE</label>
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

                                        <!-- 3. DATA -->
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

                                        <!-- 4. VIEW -->
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
                                                            type="checkbox" name="permissions[]" value="SHOW VIEW"
                                                            id="permShowView">
                                                        <label class="form-check-label" for="permShowView">SHOW
                                                            VIEW</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- 5. PROCEDURE / FUNCTION -->
                                        <div class="col-md-6">
                                            <div class="border rounded p-3 bg-light h-100">
                                                <div
                                                    class="d-flex justify-content-between align-items-center mb-2 pb-1 border-bottom">
                                                    <strong class="text-uppercase text-secondary small">ROUTINE</strong>
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

            // Targets Selectors
            const allTablesCb = document.getElementById('allTables');
            const tableCheckboxes = document.querySelectorAll('.table-checkbox');

            const allProceduresCb = document.getElementById('allProcedures');
            const procedureCheckboxes = document.querySelectorAll('.procedure-checkbox');

            const allFunctionsCb = document.getElementById('allFunctions');
            const functionCheckboxes = document.querySelectorAll('.function-checkbox');

            // Permission Groups Selectors
            const databasePermCheckboxes = document.querySelectorAll('.database-perm');
            const tableStructurePermCheckboxes = document.querySelectorAll('.table-structure-perm');
            const dataPermCheckboxes = document.querySelectorAll('.data-perm');
            const viewPermCheckboxes = document.querySelectorAll('.view-perm');
            const routinePermCheckboxes = document.querySelectorAll('.routine-perm');

            // Group Select All Checkboxes
            const groupSelectAllCbs = document.querySelectorAll('.select-all-group');

            let isBulkUpdating = false;

            // 1. Listeners untuk Checkbox User
            if (selectAllUsers) {
                selectAllUsers.addEventListener('change', function() {
                    userCheckboxes.forEach(cb => cb.checked = this.checked);
                    updatePermissionStates();
                });
            }

            function syncSelectAllUsers() {
                if (userCheckboxes.length > 0) {
                    const allChecked = Array.from(userCheckboxes).every(cb => cb.checked);
                    selectAllUsers.checked = allChecked;
                }
            }

            userCheckboxes.forEach(cb => {
                cb.addEventListener('change', function() {
                    syncSelectAllUsers();
                    updatePermissionStates();
                });
            });

            // 2. Listeners untuk Checkbox Targets
            if (allTablesCb) {
                allTablesCb.addEventListener('change', function() {
                    tableCheckboxes.forEach(cb => {
                        cb.disabled = this.checked;
                        if (this.checked) cb.checked = false;
                    });
                    updatePermissionStates();
                });
            }
            tableCheckboxes.forEach(cb => cb.addEventListener('change', updatePermissionStates));

            if (allProceduresCb) {
                allProceduresCb.addEventListener('change', function() {
                    procedureCheckboxes.forEach(cb => {
                        cb.disabled = this.checked;
                        if (this.checked) cb.checked = false;
                    });
                    updatePermissionStates();
                });
            }
            procedureCheckboxes.forEach(cb => cb.addEventListener('change', updatePermissionStates));

            if (allFunctionsCb) {
                allFunctionsCb.addEventListener('change', function() {
                    functionCheckboxes.forEach(cb => {
                        cb.disabled = this.checked;
                        if (this.checked) cb.checked = false;
                    });
                    updatePermissionStates();
                });
            }
            functionCheckboxes.forEach(cb => cb.addEventListener('change', updatePermissionStates));

            // 3. Logika Inti: Menghidupkan/Mematikan (Enable/Disable) Checkbox Hak Akses
            function updatePermissionStates() {
                const hasUserSelected = Array.from(userCheckboxes).some(cb => cb.checked);
                
                const hasTableSelected = (allTablesCb && allTablesCb.checked) || 
                                        Array.from(tableCheckboxes).some(cb => cb.checked);
                                        
                const hasRoutineSelected = (allProceduresCb && allProceduresCb.checked) ||
                                        (allFunctionsCb && allFunctionsCb.checked) ||
                                        Array.from(procedureCheckboxes).some(cb => cb.checked) ||
                                        Array.from(functionCheckboxes).some(cb => cb.checked);

                // A. DATABASE: Aktif selama ada User yang dipilih
                toggleCheckboxes(databasePermCheckboxes, hasUserSelected);

                // B. TABLE, DATA, VIEW: Aktif bila User DAN Tabel/View dipilih
                const enableTablePerms = hasUserSelected && hasTableSelected;
                toggleCheckboxes(tableStructurePermCheckboxes, enableTablePerms);
                toggleCheckboxes(dataPermCheckboxes, enableTablePerms);
                toggleCheckboxes(viewPermCheckboxes, enableTablePerms);

                // C. PROCEDURE & FUNCTION: Aktif bila User DAN Routine dipilih
                const enableRoutinePerms = hasUserSelected && hasRoutineSelected;
                toggleCheckboxes(routinePermCheckboxes, enableRoutinePerms);

                syncGroupSelectAllStates();
            }

            // Helper Function untuk Enable/Disable
            function toggleCheckboxes(checkboxes, enable) {
                checkboxes.forEach(cb => {
                    cb.disabled = !enable;
                    if (!enable) cb.checked = false;
                });
            }

            // 4. Logika 'Pilih Semua' per Grup
            groupSelectAllCbs.forEach(groupCb => {
                groupCb.addEventListener('change', function() {
                    const targetClass = this.getAttribute('data-target');
                    const childCheckboxes = document.querySelectorAll(targetClass);
                    const isChecked = this.checked;

                    isBulkUpdating = true; // Mencegah resync bertumpuk
                    childCheckboxes.forEach(cb => {
                        if (!cb.disabled) {
                            cb.checked = isChecked;
                        }
                    });
                    isBulkUpdating = false;
                    
                    syncGroupSelectAllStates();
                });
            });

            // Pastikan perubahan checkbox individual mensinkronkan header 'Pilih Semua'
            document.querySelectorAll('.perm-checkbox').forEach(cb => {
                cb.addEventListener('change', syncGroupSelectAllStates);
            });

            // 5. Helper Sinkronisasi Status Checkbox 'Pilih Semua'
            function syncGroupSelectAllStates() {
                if (isBulkUpdating) return;

                groupSelectAllCbs.forEach(parentGroupCb => {
                    const targetClass = parentGroupCb.getAttribute('data-target');
                    const children = document.querySelectorAll(targetClass);

                    if (children.length > 0) {
                        const enabledChildren = Array.from(children).filter(c => !c.disabled);
                        const allChecked = enabledChildren.length > 0 && enabledChildren.every(c => c.checked);
                        const allDisabled = children.length > 0 && Array.from(children).every(c => c.disabled);

                        parentGroupCb.disabled = allDisabled;
                        if (allDisabled) {
                            parentGroupCb.checked = false;
                        } else {
                            parentGroupCb.checked = allChecked;
                        }
                    }
                });
            }

            // Jalankan saat load awal
            updatePermissionStates();

            // Filter Live Search untuk Tabel Mahasiswa
            const searchUserInput = document.getElementById('searchUserInput');
            if (searchUserInput) {
                searchUserInput.addEventListener('keyup', function() {
                    const query = this.value.toLowerCase();
                    document.querySelectorAll('.user-row').forEach(row => {
                        const nim = row.querySelector('.search-nim')?.textContent.toLowerCase() || '';
                        const nama = row.querySelector('.search-nama')?.textContent.toLowerCase() || '';
                        const user = row.querySelector('.search-user')?.textContent.toLowerCase() || '';

                        row.style.display = (nim.includes(query) || nama.includes(query) || user.includes(query)) ? '' : 'none';
                    });
                });
            }

            // PROTEKSI: Buka status disabled saat form disubmit agar nilai dapat dikirim
            const grantForm = document.querySelector('form');
            if (grantForm) {
                grantForm.addEventListener('submit', function() {
                    document.querySelectorAll('input:disabled').forEach(cb => {
                        cb.disabled = false;
                    });
                });
            }
        });
    </script>
@endsection
